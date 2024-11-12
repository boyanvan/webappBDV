from flask_sqlalchemy import SQLAlchemy
from mysql.connector.errors import IntegrityError
from flask import Flask, request, url_for, redirect, make_response, session, jsonify, g, send_from_directory, \
    has_request_context
from flask_session import Session
from sqlalchemy.engine import row

from utils import *

from dotenv import dotenv_values
from inspect import cleandoc
from functools import wraps

config = dotenv_values(".env")

app = Flask(__name__)
app.debug = True
app.secret_key = config['APP_SECRET_KEY'].encode('utf-8')
app.config['SESSION_TYPE'] = 'sqlalchemy'  # Use SQLAlchemy to handle session storage
app.config['SESSION_USE_SIGNER'] = True    # Adds an HMAC to session cookies
app.config['SESSION_PERMANENT'] = False    # Makes the session non-permanent
app.config['SESSION_SQLALCHEMY_TABLE'] = 'flask-sessions'  # Table name
app.config['SQLALCHEMY_DATABASE_URI'] = f"mysql+pymysql://localhost/news_website" # CREATE MEEEE
app.config['SESSION_SQLALCHEMY'] = SQLAlchemy(app)

@app.route("/")
def hello_world():
    return "<p>Hello, World!</p>"

@app.after_request
def cors_bypass(response):
    response.headers['Access-Control-Allow-Origin'] = '*'
    return response

@app.route("/api/login", methods=['POST'])
def login():
    if ('userId' in session):
        return redirect(url_for('home'))
    user = request.form.get('username')
    passwd = request.form.get('password')
    if (user and passwd):

        if (not (isinstance(user, str) and len(user) > 0)):
            return "Username is empty", 400
        if (not (isinstance(passwd, str) and len(passwd) == 96)):
            return "Password is invalid", 400

        user = getUserByName(user)
        if (user is None):
            return 'Wrong username', 400
        if (user['password'] != passwd):
            return 'Wrong password', 400

        session['userId'] = user['id']
        return 'Success'
    else:
        return 'Invalid username or password', 400

@app.route("/api/register", methods=['POST'])
def register():
    user = request.form.get('username')
    passwd = request.form.get('password')
    if (user and passwd):

        if (not (isinstance(user, str) and len(user) > 0)):
            return "Username is empty", 400
        if (not (isinstance(passwd, str) and len(passwd) == 96)):
            return "Password is empty", 400

        with connection_pool.get_connection() as cxn:
            with cxn.cursor() as cursor:
                try:
                    sql = 'INSERT INTO users (username, password) VALUE (%s, %s)'
                    cursor.execute(sql, (user,passwd))
                except IntegrityError as e:
                    if (e.args[0] == 1062): # Duplicate entry
                        return 'Username is already taken', 400
                    else:
                        raise e
        return 'Success'

@app.route("/api/getUser", methods=['GET'])
def getArticles():
    userId = int( request.args.get('id') )
    with connection_pool.get_connection() as cxn:
        with cxn.cursor() as cursor:
            sql = cleandoc("""
                SELECT username
                FROM users
                WHERE id = %s
            """)
            cursor.execute(sql, (userId, ))
            users = [
                {
                    'username': u[0],
                }
                for u in cursor.fetchall()
            ]
            return jsonify(users)

@app.route("/api/getArticles", methods=['GET'])
def getArticles():
    with connection_pool.get_connection() as cxn:
        with cxn.cursor() as cursor:
            sql = cleandoc("""
                SELECT a.title, a.content, a.authorId
                FROM articles as a
            """)
            cursor.execute(sql)
            articles = [
                {
                    'title': a[0],
                    'content': a[1],
                    'authorId': a[2],
                }
                for a in cursor.fetchall()
            ]
            return jsonify(articles)

@app.route("/api/getArticle", methods=['GET'])
def getArticles():
    articleId = int( request.args.get('id') )
    with connection_pool.get_connection() as cxn:
        with cxn.cursor() as cursor:
            sql = cleandoc("""
                SELECT a.title, a.content, a.authorId
                FROM articles as a
                WHERE a.id = %s
            """)
            cursor.execute(sql, (articleId, ))
            articles = [
                {
                    'title': a[0],
                    'content': a[1],
                    'authorId': a[2],
                }
                for a in cursor.fetchall()
            ]
            return jsonify(articles)

@app.route("/api/follow", methods=['POST'])
@login_required
def followUser():
    targetName = request.form.get('username')
    if targetName:
        target = getUserByName(targetName)
        with connection_pool.get_connection() as cxn:
            with cxn.cursor() as cursor:
                try:
                    sql = """INSERT INTO followers (userId, followerId) VALUE (%s, %s)"""
                    cursor.execute(sql, (target['id'], g.user['id'], ))
                    return 'Success'
                except IntegrityError as e:
                    if (e.args[0] == 1062): # Duplicate entry
                        return 'You already follow this user', 400
                    else:
                        raise e
    else:
        return 'Invalid username', 400

@app.route("/api/unfollow", methods=['POST'])
@login_required
def unfollowUser():
    targetName = request.form.get('username')
    if targetName:
        target = getUserByName(targetName)
        with connection_pool.get_connection() as cxn:
            with cxn.cursor() as cursor:
                sql = """DELETE FROM followers WHERE userId = %s AND followerId = %s"""
                cursor.execute(sql, (target['id'], g.user['id'], ))
                if cursor.rowcount == 0:
                    return "You don't follow this user", 400
                else:
                    return 'Success'
    else:
        return 'Invalid username', 400

@app.route("/api/getNotifications", methods=['GET'])
def getNotifications():
    with connection_pool.get_connection() as cxn:
        with cxn.cursor() as cursor:
            sql = cleandoc("""
                SELECT
                    n.type,
                    n.isSeen,
                    CASE n.type
                        WHEN 'article' THEN an.articleId
                    END AS articleId
                FROM notifications as n
                INNER JOIN article_notifications as an
                ON n.type = 'article' AND n.id = an.notificationId
            """)
            cursor.execute(sql)

            notifications = []

            for n in cursor.fetchall():
                base = {
                    'type': n[0],
                    'isSeen': n[1],
                }
                if base['type'] == 'article':
                    base.update({
                        'articleId': n[2],
                    })
                else:
                    raise NotImplementedError()
                notifications.append(base)

            return jsonify(notifications)



if __name__ == "__main__":
    app.run()
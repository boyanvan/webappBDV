# import pymysql
#
# def connect():
#     return pymysql.connect(
#         host='localhost',
#         user='Anon',
#         database='biketag',
#         autocommit=True, # Auto commit ENABLED
#     )

from mysql.connector.pooling import MySQLConnectionPool

connection_pool : MySQLConnectionPool = MySQLConnectionPool(
    pool_name="the_pool",
    pool_size=20,
    pool_reset_session=True,
    host='localhost',
    database='news_db',
    user='Anon',
    autocommit=True
)

connection_pool.get_connection()
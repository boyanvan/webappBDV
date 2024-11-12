create table users
(
    id           int unsigned auto_increment
        primary key,
    username     varchar(20)          not null UNIQUE,
    password     char(96)             not null,
    isAdmin      BOOLEAN not null default false,
    created DATETIME GENERATED ALWAYS AS ( now() ) PERSISTENT
);

CREATE TABLE followers
(
    userId int unsigned NOT NULL,
    followerId int unsigned NOT NULL,
    primary key (userId, followerId),
    foreign key (userId) REFERENCES users(id),
    foreign key (followerId) REFERENCES users(id)
);

CREATE TABLE articles
(
    id int unsigned primary key auto_increment,
    authorId int unsigned NOT NULL,
    title tinytext NOT NULL,
    content text NOT NULL,
    created DATETIME GENERATED ALWAYS AS ( now() ) PERSISTENT,
    foreign key (authorId) REFERENCES users(id)
);

CREATE TABLE notifications
(
    id int unsigned auto_increment primary key,
    receiverId int unsigned NOT NULL,
    type enum('article') not null,
    isSeen bool default FALSE NOT NULL,
    created DATETIME GENERATED ALWAYS AS ( now() ) PERSISTENT,
    foreign key (receiverId) REFERENCES users(id)
);

CREATE TABLE article_notifications
(
    notificationId int unsigned primary key,
    articleId int unsigned,
    FOREIGN KEY (notificationId) REFERENCES notifications(id),
    FOREIGN KEY (articleId) REFERENCES articles(id)
);
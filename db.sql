DROP DATABASE IF EXISTS ideastockC;
CREATE DATABASE ideastockC;
use ideastockC;

DROP TABLE IF EXISTS user;
CREATE TABLE user(
	id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'ユーザーID',
	loginId VARCHAR(10) NOT NULL UNIQUE COMMENT 'ニックネーム',
	password VARCHAR(255) NOT NULL COMMENT 'パスワード',
	name VARCHAR(10) NOT NULL COMMENT 'アカウント名'
);

DROP TABLE IF EXISTS question;
CREATE TABLE question(
	id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'クエスチョンID',
	userId VARCHAR(10) NOT NULL COMMENT 'ニックネーム',
	question VARCHAR(255) NOT NULL COMMENT '質問内容',
	date DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '投稿日時',
	deleteFlg tinyINT(1) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
	FOREIGN KEY (userId) REFERENCES user(loginId)
);

DROP TABLE IF EXISTS answer;
CREATE TABLE answer(
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'アンサーID',
    questionId INT(11) NOT NULL COMMENT 'クエスチョンID',
	userId VARCHAR(10) NOT NULL COMMENT 'ニックネーム',
    answer VARCHAR(255) NOT NULL COMMENT '回答内容',
    date DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '投稿日時',
    deleteFlg tinyINT(1) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
    FOREIGN KEY (userId) REFERENCES user(loginId),
    FOREIGN KEY (questionId) REFERENCES question(id)
);

SHOW CREATE TABLE user \G;
DESC user;
delete from question where id = 1;

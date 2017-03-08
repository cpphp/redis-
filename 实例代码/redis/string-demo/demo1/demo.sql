--创建文章表
create table article(
	id int primary key auto_increment,
	title varchar(80) not null,
	uid int not null,
	num int not null comment '文章阅读量',
	key(title)
)engine=innodb default charset=utf8;

create table article_detail(
	id int primary key auto_increment,
	aid int not null comment '关联文章表',
	content text not null,
	key(aid)
)engine=innodb default charset=utf8;

insert into article(title,uid,num) values('文章1', 2, 1000);
insert into article_detail(aid,content) values(1, '文章1的内容');

insert into article(title,uid,num) values('文章2', 2, 100);
insert into article_detail(aid,content) values(2, '文章2的内容');

insert into article(title,uid,num) values('文章3', 33, 2000);
insert into article_detail(aid,content) values(3, '文章3的内容');
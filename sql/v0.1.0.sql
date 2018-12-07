drop table if exists user;
create table user (
  id int unsigned not null auto_increment,
  affiliate char(6) not null,
  plan tinyint(1) not null default 0 comment '0:member;1:vip;2:partner;3:super',
  nickname varchar(50) not null default '',
  email varchar(50),
  mobile varchar(50),
  status tinyint(1) not null default 1 comment '0:disabled;1:enabled',
  create_time timestamp not null default current_timestamp,
  update_time timestamp,
  facebook json,
  fid varchar(50) generated always as (facebook ->> '$.id'),
  primary key (id),
  unique idx_affiliate (affiliate),
  unique idx_user_email (email),
  unique idx_user_mobile (mobile),
  unique idx_user_facebook (fid)
);
insert into user (id,affiliate,plan,nickname,status,update_time) values (1,'888888',1,'ezd',1,now());

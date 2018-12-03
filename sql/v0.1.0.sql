drop table if exists user;
create table user (
  id int unsigned not null auto_increment,
  affiliate char(6) not null comment 'one\'s affiliate code',
  referral char(6) not null comment 'referral\'s affiliate code',
  email varchar(50),
  mobile varchar(50),
  status tinyint(1) not null default 1 comment '0:disabled,1:enabled',
  create_time timestamp not null default current_timestamp,
  update_time timestamp,
  facebook json,
  fid varchar(50) generated always as (facebook ->> '$.id'),
  primary key (id),
  index idx_referral (referral),
  unique idx_affiliate (affiliate),
  unique idx_user_email (email),
  unique idx_user_mobile (mobile),
  unique idx_user_facebook (fid)
);

insert into user (id,affiliate,referral,status,facebook,update_time) values (1,'888888','888888',1,'{"name": "Vivian Lim","id": "123805028625200"}',now());
select * from user where facebook->'$.id'='123805028625200';
select facebook->'$.id',facebook->'$.name' from user;
select fid from user;

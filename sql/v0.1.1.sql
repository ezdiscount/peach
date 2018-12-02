drop table if exists user_plan;
create table user_plan (
  id int unsigned not null auto_increment,
  level tinyint(1) not null default 0 comment '0:普通会员,1:付费会员,2:梦想合伙人,3:超级合伙人',
  primary key (id)
) comment '梦想合伙人:成功邀请五位付费会员,超级合伙人:邀请的付费会员中产生了五位梦想合伙人';

insert into user_plan (id,level) values (0,0),(1,1),(2,2),(3,3);

alter table user add column nickname varchar(50) default '', add column plan tinyint(1) not null default 0;
update user set nickname='ezd', plan=0 where id=0;

drop table if exists quota;
create table quota (
  id int unsigned not null auto_increment,
  budget int not null default 0,
  primary key (id)
) comment '所有金额单位为人民币分,1表示0.01元,100表示1元';

################################################################################
# 每加入一个付费会员触发：
#     plan从普通会员升级为付费会员
#     检查邀请者是否升级为梦想合伙人
#     如果邀请者升级为梦想合伙人，检查邀请者的邀请者是否升级为超级合伙人
# 每产生一笔佣金触发：
#     1.顾客为普通会员
#     2.顾客为付费会员
#     3.顾客为梦想合伙人
#     4.顾客为超级合伙人
################################################################################

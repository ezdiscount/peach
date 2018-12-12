drop table if exists config;
create table config (
  id int unsigned not null auto_increment,
  name varchar(50) not null default '',
  description varchar(500) not null default '',
  data json,
  version tinyint(1) not null default 1,
  index idx_meta_name (name),
  primary key (id)
);
insert into config (name,description,data) values ('upgrade_user_threshold','用户升级条件',json_object('vip_to_partner',100,'partner_to_super',10)), ('quota_rate','佣金分配参数',json_object('buyer_rate',0.5,'father_rate',0.1,'grandpa_rate',0.1,'chain_super_rate',0.2,'chain_partner_rate',0.15,'super_rate',0.05));

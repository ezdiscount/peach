################################################################################
# 普通会员付费触发事件
################################################################################
# 本人升级为付费会员
# 检查邀请人升级梦想合伙人条件：付费会员>=100
# 如果邀请人升级为梦想合伙人，检查邀请人的邀请人升级为超级合伙人条件：梦想合伙人>=10
################################################################################
drop table if exists user_chain;
create table user_chain (
  id int unsigned not null auto_increment,
  user int unsigned not null,
  user_plan tinyint(1) not null default 0,
  mentor int unsigned not null,
  mentor_plan tinyint(1) not null default 0,
  length smallint unsigned not null default 1,
  index idx_user_chain_user (user),
  index idx_user_chain_mentor_plan (mentor_plan),
  primary key (id)
);
insert into user_chain (id,user,user_plan,mentor,mentor_plan,length) values (1,1,1,0,0,0);

################################################################################
# 佣金展示（商品展示）
################################################################################
# 普通会员无佣金
# 其他会员，显示自购返佣（总佣金的50%）
################################################################################
# 佣金分配
################################################################################
# 50% 自购佣金
# 10% 分销佣金
#   追溯两级，共20%
# 20% 奖金池
#   仅梦想合伙人和超级合伙人
#     A->B->C-D->E->F(梦想)->G->H->I(超级)->J->K: f 15%, I 5%
#     A->B->C-D->E->F(梦想)->G->H->I(梦想)->J->K: f 15%, I 5%
#     A->B->C-D->E->F(超级)->G->H->I(梦想)->J->K: f 20%, I 0
# 5% 平级奖
#   仅限：超级合伙人A发展超级合伙人B，同时B没有发展任何超级合伙人，则A获取5%的平级奖
# 5% 结余
################################################################################
drop table if exists quota;
create table quota (
  id int unsigned not null auto_increment,
  tb_order varchar(50) not null comment '淘宝订单号',
  budget int not null default 0,
  status tinyint(1) not null default 0 comment '0:init;1:auto(系统计算完毕)',
  buyer int unsigned not null default 0 comment '购买人id',
  buyer_plan tinyint(1) not null default 0,
  buyer_earning int not null default 0 comment 'buyer_plan == 0 ? 0 or floor(budget*0.5)',
  father int unsigned not null default 0 comment '一级邀请人id',
  father_plan tinyint(1) not null default 0,
  father_earning int not null default 0 comment '0 or floor(budget*0.1)',
  grandpa int unsigned not null default 0 comment '二级邀请人id',
  grandpa_plan tinyint(1) not null default 0,
  grandpa_earning int not null default 0 comment '0 or floor(budget*0.1)',
  chain_a_earner int unsigned not null default 0 comment 'nearest partner or super',
  chain_a_plan tinyint(1) not null default 0,
  chain_a_earning int not null default 0 comment 'partner:floor(budget*0.15);super:floor(budget*0.2)',
  chain_b_earner int unsigned not null default 0 comment 'secondary nearest partner or super only when chain_a_plan is partner',
  chain_b_plan tinyint(1) not null default 0,
  chain_b_earning int not null default 0 comment 'floor(budget*0.05)',
  super_earner int not null default 0,
  super_earning int not null default 0 comment 'super_earner!=0:floor(budget*0.05);others:0',
  margin int not null default 0 comment 'budget-buyer_earning-father_earning-grandpa_earning-chain_a_earning-chain_b_earning-super_earning',
  primary key (id)
) comment '所有金额单位为人民币分:1表示0.01元;100表示1元';

drop table if exists product;
create table product (
  id int unsigned primary key auto_increment,
  affiliate varchar(100) not null default 'www',
  hc tinyint(1) not null default 0 comment '0:普通佣金;1:高分佣',
  status tinyint(1) not null default 1 COMMENT '0:pending;1:ready;2:remove',
  weight int unsigned not null default 0,
  create_time timestamp not null default current_timestamp,
  # from excel:
  tid bigint not null default 0,
  title varchar(1000) not null default '',
  thumb varchar(1000) not null default '',
  url varchar(100) not null default '' comment '短链接',
  code varchar(100) not null default '' comment '短码',
  price int unsigned not null default 0 COMMENT '人民币分',
  coupon int unsigned not null default 0 COMMENT '人民币分',
  expire_date date not null default '2018-01-01',
  commission_rate tinyint(2) unsigned not null default 0 comment '百分数值:1代表1%',
  commission_value int unsigned not null default 0 comment '人民币分'
) engine =InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;
create index index_product_affiliate on product (affiliate);
create unique index index_product_affiliate_tid on product (affiliate, tid);

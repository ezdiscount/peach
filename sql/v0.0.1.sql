drop table if exists admin;
create table admin (
  id int(20) unsigned not null auto_increment,
  username varchar(100) not null,
  password varchar(100) not null,
  email varchar(100) not null default '',
  mobile varchar(100) not null default '',
  status tinyint(1) not null default 0 comment '0:disabled;1:enabled',
  create_time timestamp not null default current_timestamp,
  primary key (id),
  unique index idx_admin_username (username)
);

drop table if exists product_raw;
create table product_raw (
  id int unsigned primary key auto_increment,
  affiliate varchar(100) not null default 'www',
  status tinyint(1) not null default 0 comment '0:pending;1:ready;2:remove',
  weight int unsigned not null default 0,
  create_time timestamp not null default CURRENT_TIMESTAMP,
  # from excel:
  tid bigint not null default 0,
  title varchar(500) not null default '',
  thumb varchar(1000) not null default '',
  detailUrl varchar(1000) not null default '',
  store varchar(100) not null default '',
  price int unsigned not null default 0 comment '人民币分',
  saleVolume int unsigned not null default 0,
  commissionRate tinyint(2) unsigned not null default 0 comment '百分数值:1代表1%',
  commissionValue int unsigned not null default 0 comment '人民币分',
  sellerWaWa varchar(100) not null default '',
  tkShortUrl varchar(100) not null default '',
  tkUrl varchar(1000) not null default '',
  tkCode varchar(100) not null default '',
  couponTotal int unsigned not null default 0,
  couponAvailable int unsigned not null default 0,
  couponValue varchar(100) not null default '',
  couponStart timestamp not null default '2018-01-01',
  couponEnd  timestamp not null default '2018-01-01',
  couponUrl varchar(1000) not null default '',
  couponCode varchar(100) not null default '',
  couponShortUrl varchar(100) not null default '',
  isRecommend tinyint(1) not null default 0,
  groupThresh int unsigned not null default 0,
  groupPrice int unsigned not null default 0 comment '人民币分',
  groupCommission int unsigned not null default 0 comment '人民币分',
  groupStart timestamp not null default '2018-01-01',
  groupEnd timestamp not null default '2018-01-01'
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;
create index index_product_affiliate ON product_raw (affiliate);
create unique index index_product_affiliate_tid ON product_raw (affiliate, tid);

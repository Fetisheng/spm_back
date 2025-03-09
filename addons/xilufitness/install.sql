-- ----------------------------
-- Table structure for __PREFIX__xilufitness_activity_coupon
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_activity_coupon` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) DEFAULT NULL COMMENT '所属小程序',
  `title` varchar(30) DEFAULT NULL COMMENT '优惠券名称',
  `meet_amount` decimal(10,0) unsigned DEFAULT '0' COMMENT '满足金额',
  `discount_amount` decimal(10,0) unsigned DEFAULT '0' COMMENT '抵扣金额',
  `expire_day` int(3) unsigned DEFAULT '0' COMMENT '有效期',
  `coupon_count` int(10) unsigned DEFAULT '0' COMMENT '发放数量',
  `receive_count` int(10) unsigned DEFAULT '0' COMMENT '领取数量',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `is_activity` tinyint(1) unsigned DEFAULT '0' COMMENT '是否参与活动',
  `invite_num` tinyint(6) unsigned DEFAULT '0' COMMENT '邀请人数',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`) USING BTREE,
  KEY `status_title` (`status`,`title`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='代金券管理';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_activity_medal
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_activity_medal` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `medal_name` varchar(30) DEFAULT NULL COMMENT '勋章名称',
  `thumb_image` varchar(150) DEFAULT NULL COMMENT '勋章图片',
  `thumb_active_image` varchar(150) DEFAULT NULL COMMENT '解锁勋章图片',
  `class_time` bigint(16) DEFAULT NULL COMMENT '训练时长',
  `description` text COMMENT '勋章说明',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='勋章管理';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_activity_recharge
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_activity_recharge` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `recharge_amount` decimal(10,0) DEFAULT NULL COMMENT '充值金额',
  `account_amount` decimal(10,0) DEFAULT NULL COMMENT '到账金额',
  `cut_amount` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '节省金额',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_id_status` (`brand_id`,`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='充值设置';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_admin_access
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_admin_access` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(30) unsigned DEFAULT '0' COMMENT '所属管理员',
  `brand_id` int(30) DEFAULT NULL COMMENT '所属小程序',
  `account_type` tinyint(2) unsigned DEFAULT '0' COMMENT '账号类型 1 小程序 2 门店',
  `shop_id` int(30) unsigned DEFAULT '0' COMMENT '所属门店',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `admin_brand_id` (`admin_id`,`brand_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='小程序账号管理';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_auth_group
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(30) DEFAULT NULL COMMENT '所属权限组',
  `is_type` tinyint(2) unsigned DEFAULT '0' COMMENT '组类型 1 小程序 2 门店',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COMMENT='小程序权限组';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_banner
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_banner` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) DEFAULT NULL COMMENT '所属小程序',
  `title` varchar(20) DEFAULT NULL COMMENT '名称',
  `thumb_image` varchar(150) DEFAULT NULL COMMENT '图片',
  `banner_position` tinyint(2) unsigned DEFAULT '0' COMMENT '广告位置',
  `is_redirect` tinyint(2) unsigned DEFAULT '0' COMMENT '是否跳转',
  `data_id` int(10) DEFAULT NULL COMMENT '跳转id',
  `weigh` int(10) DEFAULT NULL COMMENT '排序',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_id_position` (`brand_id`,`banner_position`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='轮播图片管理';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_brand
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_brand` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) DEFAULT NULL COMMENT '登录账号',
  `brand_name` varchar(30) DEFAULT NULL COMMENT '小程名称',
  `brand_mobile` varchar(12) DEFAULT NULL COMMENT '小程手机号',
  `brand_email` varchar(30) DEFAULT NULL COMMENT '小程邮箱',
  `brand_logo_image` varchar(150) DEFAULT NULL COMMENT '小程logo',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `brand_key` varchar(25) DEFAULT NULL COMMENT '小程Key',
  `deletetime` bigint(16) unsigned DEFAULT NULL COMMENT '删除时间',
  `updatetime` bigint(16) unsigned DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_key` (`brand_key`) USING BTREE,
  KEY `brand_name_mobile` (`brand_name`,`brand_mobile`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='小程序管理';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_brand_config
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_brand_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `name` varchar(30) DEFAULT '' COMMENT '变量名',
  `group` varchar(30) DEFAULT '' COMMENT '分组',
  `title` varchar(100) DEFAULT '' COMMENT '变量标题',
  `tip` varchar(100) DEFAULT '' COMMENT '变量描述',
  `type` varchar(30) DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
  `visible` varchar(255) DEFAULT '' COMMENT '可见条件',
  `value` text COMMENT '变量值',
  `content` text COMMENT '变量字典数据',
  `rule` varchar(100) DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) DEFAULT '' COMMENT '扩展属性',
  `setting` varchar(255) DEFAULT '' COMMENT '配置',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `brand_id` (`brand_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COMMENT='基本配置';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_camp
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_camp` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `title` varchar(50) DEFAULT NULL COMMENT '活动名称',
  `thumb_image` varchar(150) DEFAULT NULL COMMENT '缩略图',
  `thumb_images` text COMMENT '详情轮播图集',
  `lable_ids` varchar(50) DEFAULT NULL COMMENT '活动标签',
  `description` varchar(255) DEFAULT NULL COMMENT '活动简介',
  `content` text COMMENT '活动详情',
  `tip_content` text COMMENT '注意事情',
  `camp_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '报名单价',
  `write_off_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '核销收入',
  `market_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '市场价',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `updatetime` bigint(16) unsigned DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`) USING BTREE,
  KEY `status_title` (`status`,`title`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='活动列表';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_cashflow
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_cashflow` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `user_id` int(30) unsigned DEFAULT '0' COMMENT '所属用户',
  `order_type` tinyint(2) DEFAULT NULL COMMENT '订单类型',
  `order_no` varchar(30) DEFAULT NULL COMMENT '订单号',
  `pay_amount` decimal(10,2) DEFAULT NULL COMMENT '支付金额',
  `pay_status` tinyint(1) unsigned DEFAULT '0' COMMENT '支付状态',
  `pay_time` bigint(16) DEFAULT NULL COMMENT '支付时间',
  `pay_type` tinyint(1) unsigned DEFAULT '0' COMMENT '支付方式',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_user_id` (`brand_id`,`user_id`) USING BTREE,
  KEY `order_no` (`order_no`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='支付流水记录';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_cms
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_cms` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) DEFAULT NULL COMMENT '所属小程序',
  `title` varchar(30) DEFAULT NULL COMMENT '名称',
  `description` varchar(150) DEFAULT NULL COMMENT '简介',
  `cms_type` tinyint(2) DEFAULT NULL COMMENT '类型',
  `content` text COMMENT '详情',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_status` (`brand_id`,`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COMMENT='单页管理';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_coach
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_coach` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(50) DEFAULT '0' COMMENT '所属小程序',
  `shop_ids` varchar(50) DEFAULT NULL COMMENT '所属门店',
  `coach_group_id` int(10) DEFAULT NULL COMMENT '所属等级',
  `coach_name` varchar(30) DEFAULT NULL COMMENT '教练名称',
  `coach_mobile` varchar(12) DEFAULT NULL COMMENT '手机号',
  `coach_avatar` varchar(150) DEFAULT NULL COMMENT '头像',
  `coach_images` text COMMENT '教练图集',
  `coach_sex` enum('male','female','unknow') DEFAULT 'unknow' COMMENT '性别',
  `lable_ids` varchar(50) DEFAULT NULL COMMENT '所属标签',
  `coach_info` varchar(255) DEFAULT NULL COMMENT '教练简介',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `updatetime` bigint(16) unsigned DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `shop_brand_ids` (`shop_ids`,`brand_id`) USING BTREE,
  KEY `coach_name_mobile` (`coach_name`,`coach_mobile`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='教练列表';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_coach_account
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_coach_account` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) DEFAULT NULL COMMENT '所属小程序',
  `coach_id` int(30) DEFAULT NULL COMMENT '所属教练',
  `total_account` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '总收入',
  `account` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '可用余额',
  `withdraw_account` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '提现总金额',
  `course_count` int(10) unsigned DEFAULT '0' COMMENT '上课数量',
  `class_duration` int(10) unsigned DEFAULT '0' COMMENT ' 总时长',
  `course_total_count` int(10) unsigned DEFAULT '0' COMMENT '上课总人数',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_coach_id` (`brand_id`,`coach_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='教练账户';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_coach_cash
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_coach_cash` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `coach_id` int(30) DEFAULT NULL COMMENT '所属教练',
  `title` varchar(50) DEFAULT NULL COMMENT '收入来源',
  `is_type` tinyint(2) unsigned DEFAULT '1' COMMENT '类型',
  `data_id` int(30) unsigned DEFAULT '0' COMMENT '所属外建',
  `work_course_id` int(30) unsigned DEFAULT '0' COMMENT '所属排期',
  `cash_type` tinyint(2) unsigned DEFAULT '1' COMMENT '资金类型',
  `cash_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '收入金额',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_coach_id` (`brand_id`,`coach_id`) USING BTREE,
  KEY `work_course_id` (`work_course_id`,`data_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COMMENT='教练收入';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_coach_group
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_coach_group` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) DEFAULT NULL COMMENT '所属小程序',
  `group_name` varchar(30) DEFAULT NULL COMMENT '等级名称',
  `group_image` varchar(150) DEFAULT NULL COMMENT '等级图片',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `status_brand_id` (`status`,`brand_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='教练等级管理';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_coach_report
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_coach_report` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `coach_id` int(30) DEFAULT NULL COMMENT '所属教练',
  `start_at` bigint(16) DEFAULT NULL COMMENT '开始时间',
  `end_at` bigint(16) DEFAULT NULL COMMENT '结束时间',
  `description` varchar(100) DEFAULT NULL COMMENT '请假事由',
  `report_status` tinyint(2) unsigned DEFAULT '0' COMMENT '状态',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`) USING BTREE,
  KEY `coach_id` (`coach_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='请假报备';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_coach_withdraw
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_coach_withdraw` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `coach_id` int(30) unsigned DEFAULT '0' COMMENT '所属教练',
  `order_no` varchar(36) DEFAULT NULL COMMENT '提现单号',
  `withdraw_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '提现金额',
  `withdraw_status` tinyint(2) unsigned DEFAULT '0' COMMENT '状态',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_coach_id` (`brand_id`,`coach_id`) USING BTREE,
  KEY `order_no` (`order_no`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COMMENT='教练提现记录';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_course
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_course` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `course_cate_pid` int(10) unsigned DEFAULT '0' COMMENT '所属课程父级分类',
  `course_cate_id` int(10) unsigned DEFAULT '0' COMMENT '所属课程分类',
  `course_type` tinyint(2) unsigned DEFAULT '0' COMMENT '课程类型',
  `title` varchar(50) DEFAULT NULL COMMENT '课程名称',
  `thumb_image` varchar(150) DEFAULT NULL COMMENT '缩略图',
  `thumb_images` text COMMENT '详情轮播图集',
  `lable_ids` varchar(50) DEFAULT NULL COMMENT '课程标签',
  `content` text COMMENT '课程详情',
  `tip_content` text COMMENT '注意事情',
  `course_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '课程单价',
  `write_off_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '教练核销收入',
  `market_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '市场价',
  `is_index` tinyint(2) unsigned DEFAULT '0' COMMENT '推荐首页',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `updatetime` bigint(16) unsigned DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`) USING BTREE,
  KEY `status_title` (`status`,`title`) USING BTREE,
  KEY `course_type` (`course_type`) USING BTREE,
  KEY `cate_id` (`course_cate_pid`,`course_cate_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='课程列表';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_course_cate
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_course_cate` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `pid` int(10) unsigned DEFAULT '0' COMMENT '所属父级',
  `cate_name` varchar(30) DEFAULT NULL COMMENT '分类名称',
  `weigh` int(10) DEFAULT NULL COMMENT '排序',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_id_status` (`brand_id`,`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='课程分类';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_lable
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_lable` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `lable_name` varchar(30) DEFAULT NULL COMMENT '标签名称',
  `lable_type` enum('course','coach','camp') DEFAULT 'course' COMMENT '标签类型',
  `weigh` int(10) unsigned DEFAULT NULL COMMENT '排序',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `status_lable_type` (`status`,`lable_type`) USING BTREE,
  KEY `brand_id` (`brand_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='标签管理';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_order
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_order` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `shop_id` int(30) unsigned DEFAULT '0' COMMENT '所属门店',
  `user_id` int(30) unsigned DEFAULT '0' COMMENT '所属用户',
  `coupon_id` int(30) unsigned DEFAULT '0' COMMENT '所属优惠券',
  `coach_id` int(30) unsigned DEFAULT '0' COMMENT '所属教练',
  `data_id` int(30) unsigned DEFAULT '0' COMMENT '业务外间',
  `course_camp_id` int(30) unsigned DEFAULT '0' COMMENT '课程/活动id',
  `trade_no` varchar(50) DEFAULT NULL COMMENT '交易订单号',
  `order_no` varchar(50) DEFAULT NULL COMMENT '订单号',
  `re_no` varchar(50) DEFAULT NULL COMMENT '退款单号',
  `coupon_amount` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '优惠金额',
  `pay_amount` decimal(10,2) DEFAULT NULL COMMENT '支付金额',
  `total_amount` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '总金额',
  `pay_status` tinyint(1) unsigned DEFAULT '0' COMMENT '支付状态',
  `order_status` tinyint(2) unsigned DEFAULT '0' COMMENT '支付状态',
  `pay_time` bigint(16) DEFAULT NULL COMMENT '支付时间',
  `pay_type` tinyint(1) unsigned DEFAULT '2' COMMENT '支付方式',
  `order_type` tinyint(1) DEFAULT NULL COMMENT '订单类型',
  `code_num` int(3) unsigned DEFAULT '0' COMMENT '核销次数',
  `starttime` bigint(16) DEFAULT NULL COMMENT '开课时间',
  `endtime` bigint(16) DEFAULT NULL COMMENT '开课时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '结束时间',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `order_no` (`order_no`) USING BTREE,
  KEY `brand_user_id` (`brand_id`,`user_id`) USING BTREE,
  KEY `pay_order_status` (`order_status`,`pay_status`) USING BTREE,
  KEY `data_id_type` (`data_id`,`order_type`) USING BTREE,
  KEY `course_camp_id` (`course_camp_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='订单列表';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_order_goods
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_order_goods` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(100) DEFAULT NULL COMMENT '所属订单',
  `order_type` tinyint(1) unsigned DEFAULT NULL COMMENT '订单类型',
  `goods_id` int(50) DEFAULT NULL COMMENT '所属产品',
  `course_camp_id` int(30) unsigned DEFAULT '0' COMMENT '所属课程/活动id',
  `goods_name` varchar(50) DEFAULT NULL COMMENT '产品名称',
  `thumb_image` varchar(150) DEFAULT NULL COMMENT '产品图片',
  `num` int(4) DEFAULT NULL COMMENT '数量',
  `sale_price` decimal(10,2) DEFAULT NULL COMMENT '销售单价',
  `total_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '总金额',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `order_id_type` (`order_id`,`order_type`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='订单明细';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_order_verification_records
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_order_verification_records` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `order_id` int(30) unsigned DEFAULT '0' COMMENT '所属订单',
  `coach_id` int(30) unsigned DEFAULT '0' COMMENT '所属教练',
  `shop_id` int(30) unsigned DEFAULT '0' COMMENT '所属门店',
  `user_id` int(30) unsigned DEFAULT '0' COMMENT '所属用户',
  `data_id` int(30) unsigned DEFAULT '0' COMMENT '排课id',
  `check_time` bigint(16) DEFAULT NULL COMMENT '核销时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_order_id` (`brand_id`,`order_id`) USING BTREE,
  KEY `coach_shop_id` (`coach_id`,`shop_id`) USING BTREE,
  KEY `user_data_id` (`user_id`,`data_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单核销记录';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_point_rule
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_point_rule` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `title` varchar(30) DEFAULT NULL COMMENT '规则名称',
  `rule_type` tinyint(2) unsigned DEFAULT '1' COMMENT '规则类型',
  `point_type` tinyint(1) unsigned DEFAULT '1' COMMENT '积分类型',
  `point_amount` int(6) DEFAULT NULL COMMENT '积分值',
  `point_ratio` varchar(5) DEFAULT NULL COMMENT '比例',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`) USING BTREE,
  KEY `point_ratio_status` (`status`,`point_ratio`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='积分规则';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_shop
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_shop` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `username` varchar(30) DEFAULT NULL COMMENT '登录账号',
  `password` varchar(30) DEFAULT NULL COMMENT '登录密码',
  `shop_name` varchar(30) DEFAULT NULL COMMENT '门店名称',
  `shop_image` varchar(150) DEFAULT NULL COMMENT '门店图片',
  `shop_images` text COMMENT '详情图集',
  `shop_mobile` varchar(12) DEFAULT NULL COMMENT '门店电话',
  `province_id` int(10) unsigned DEFAULT '0' COMMENT '所属省份',
  `city_id` int(10) unsigned DEFAULT '0' COMMENT '所属市',
  `area_id` int(10) unsigned DEFAULT '0' COMMENT '所属区',
  `address` varchar(50) DEFAULT NULL COMMENT '详细地址',
  `lat` varchar(30) DEFAULT NULL COMMENT '纬度',
  `lng` varchar(30) DEFAULT NULL COMMENT '经度',
  `star` int(2) unsigned DEFAULT '0' COMMENT '评分',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`) USING BTREE,
  KEY `status_name` (`status`,`shop_name`) USING BTREE,
  KEY `shop_mobile` (`shop_mobile`) USING BTREE,
  KEY `province_city_area_id` (`city_id`,`province_id`,`area_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='门店列表';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_user
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_user` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(100) DEFAULT NULL COMMENT '关联用户',
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `nickname` varchar(30) DEFAULT NULL COMMENT '昵称',
  `avatar` varchar(150) DEFAULT NULL COMMENT '头像',
  `gender` tinyint(1) unsigned DEFAULT '0' COMMENT '性别',
  `mobile` varchar(12) DEFAULT NULL COMMENT '手机号',
  `point` int(9) unsigned DEFAULT '0' COMMENT '积分',
  `account` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '余额',
  `train_day` int(6) unsigned DEFAULT '0' COMMENT '训练天数',
  `train_duration` decimal(6,1) unsigned DEFAULT '0.0' COMMENT '训练时长',
  `train_count` int(6) unsigned DEFAULT '0' COMMENT '训练次数',
  `is_vip` tinyint(2) unsigned DEFAULT '0' COMMENT '会员',
  `loginip` varchar(30) DEFAULT NULL COMMENT '登录ip',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `mobile_nickname` (`mobile`,`nickname`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='会员列表';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_user_account
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_user_account` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned NOT NULL DEFAULT '0' COMMENT '所属小程序',
  `user_id` int(30) unsigned NOT NULL DEFAULT '0' COMMENT '所属用户',
  `data_id` int(30) unsigned DEFAULT '0' COMMENT '关联表的id',
  `title` varchar(30) DEFAULT NULL COMMENT '变动说明',
  `before_account` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '变动之前余额',
  `after_account` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '变动之后余额',
  `amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '余额值',
  `account_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型',
  `amount_type` tinyint(1) unsigned DEFAULT '1' COMMENT '余额变动类型',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_user_id` (`brand_id`,`user_id`) USING BTREE,
  KEY `data_id` (`data_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COMMENT='用户余额明细';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_user_collect
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_user_collect` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(30) unsigned DEFAULT '0' COMMENT '所属用户',
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `shop_id` int(30) unsigned DEFAULT '0' COMMENT '所属门店',
  `is_type` tinyint(2) DEFAULT NULL COMMENT '类型',
  `data_id` int(30) DEFAULT NULL COMMENT '所属外健',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_user_id` (`brand_id`,`user_id`) USING BTREE,
  KEY `data_id` (`data_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='用户收藏';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_user_comment
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_user_comment` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `user_id` int(30) unsigned DEFAULT '0' COMMENT '所属用户',
  `shop_id` int(30) unsigned DEFAULT '0' COMMENT '所属门店',
  `coach_id` int(30) DEFAULT NULL COMMENT '所属教练',
  `course_camp_id` int(30) unsigned DEFAULT '0' COMMENT '所属课程/活动 id',
  `order_id` int(30) unsigned DEFAULT '0' COMMENT '所属订单',
  `profession_star` int(2) unsigned DEFAULT '0' COMMENT '专业度',
  `affinity_star` int(2) unsigned DEFAULT '0' COMMENT '亲和力',
  `impression_star` int(2) unsigned DEFAULT '0' COMMENT '印象',
  `content` varchar(255) DEFAULT NULL COMMENT '评论内容',
  `star` int(2) unsigned DEFAULT '0' COMMENT '星级',
  `status` char(6) DEFAULT 'normal' COMMENT '状态',
  `course_type` tinyint(2) unsigned DEFAULT '0' COMMENT '课程类型',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_users_id` (`brand_id`,`user_id`) USING BTREE,
  KEY `shop_id` (`shop_id`) USING BTREE,
  KEY `coach_id` (`coach_id`) USING BTREE,
  KEY `course_camp_id` (`course_camp_id`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='用户评论';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_user_connect
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_user_connect` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `account_user_id` int(30) unsigned DEFAULT '0' COMMENT '所属用户',
  `openid` varchar(50) DEFAULT NULL COMMENT '小程序openid',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`) USING BTREE,
  KEY `openid_user_id` (`openid`,`account_user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='小程序用户信息';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_user_coupon
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_user_coupon` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned NOT NULL DEFAULT '0' COMMENT '所属小程序',
  `user_id` int(30) unsigned NOT NULL DEFAULT '0' COMMENT '所属用户',
  `coupon_id` int(30) unsigned NOT NULL DEFAULT '0' COMMENT '所属优惠券',
  `title` varchar(30) NOT NULL COMMENT '优惠券名称',
  `meet_amount` decimal(10,0) unsigned NOT NULL DEFAULT '0' COMMENT '满足金额',
  `discount_amount` decimal(10,0) unsigned NOT NULL DEFAULT '0' COMMENT '抵扣金额',
  `expire_time` bigint(16) NOT NULL COMMENT '有效期',
  `coupon_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `invite_num` int(10) unsigned DEFAULT '0' COMMENT '邀请人数',
  `updatetime` bigint(16) NOT NULL COMMENT '更新时间',
  `createtime` bigint(16) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_user_id` (`brand_id`,`user_id`) USING BTREE,
  KEY `coupon_id` (`coupon_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='用户领取优惠券记录';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_user_media
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_user_media` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属用户',
  `user_id` int(30) unsigned DEFAULT NULL COMMENT '所属用户',
  `media_id` int(30) unsigned DEFAULT '0' COMMENT '所属勋章',
  `train_duration` decimal(10,1) unsigned DEFAULT '0.0' COMMENT '训练时长',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='解锁勋章记录';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_user_point
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_user_point` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned NOT NULL DEFAULT '0' COMMENT '所属小程序',
  `user_id` int(30) unsigned NOT NULL DEFAULT '0' COMMENT '所属用户',
  `data_id` int(30) unsigned DEFAULT '0' COMMENT '关联表的id',
  `title` varchar(30) DEFAULT NULL COMMENT '变动说明',
  `before_point` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '变动之前积分',
  `after_point` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '变动之后积分',
  `point` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '积分值',
  `rule_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型',
  `point_type` tinyint(1) unsigned DEFAULT '1' COMMENT '积分变动类型',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_user_id` (`brand_id`,`user_id`) USING BTREE,
  KEY `data_id` (`data_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COMMENT='用户积分明细';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_user_share_record
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_user_share_record` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `user_id` int(30) unsigned DEFAULT '0' COMMENT '所属用户',
  `rec_user_id` int(30) unsigned DEFAULT '0' COMMENT '推荐人id',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_rec_user_id` (`brand_id`,`rec_user_id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='推荐记录';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_work_camp
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_work_camp` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `camp_id` int(30) DEFAULT '0' COMMENT '所属活动',
  `shop_id` int(30) unsigned DEFAULT '0' COMMENT '所属门店',
  `coach_id` int(30) unsigned DEFAULT '0' COMMENT '所属教练',
  `start_at` bigint(16) DEFAULT NULL COMMENT '开始时间',
  `end_at` bigint(16) DEFAULT NULL COMMENT '结束时间',
  `class_duration` varchar(3) DEFAULT NULL COMMENT '课时时长',
  `class_count` int(3) unsigned DEFAULT '1' COMMENT '课时数',
  `sign_count` int(2) unsigned DEFAULT '1' COMMENT '用户报名人数',
  `camp_count` int(5) unsigned DEFAULT '0' COMMENT '开营人数',
  `total_count` int(5) unsigned DEFAULT '0' COMMENT '满员人数',
  `description` varchar(255) DEFAULT NULL COMMENT '其他说明',
  `camp_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '活动价格',
  `write_off_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '核销收入',
  `market_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '市场价',
  `status` enum('normal','hidden','complete','failed') DEFAULT 'normal' COMMENT '状态',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_shop_id` (`brand_id`,`shop_id`) USING BTREE,
  KEY `camp_coach_id` (`camp_id`,`coach_id`) USING BTREE,
  KEY `start_end_at` (`start_at`,`end_at`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='活动排课';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_work_camp_plan
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_work_camp_plan` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT,
  `work_camp_id` int(30) DEFAULT NULL COMMENT '所属活动',
  `day_date` bigint(16) DEFAULT NULL COMMENT '日期',
  `day_start_at` varchar(15) DEFAULT NULL COMMENT '开始时间',
  `day_end_at` varchar(15) DEFAULT NULL COMMENT '结束时间',
  `week` int(2) unsigned DEFAULT NULL COMMENT '周几',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `work_camp_id` (`work_camp_id`) USING BTREE,
  KEY `start_end_at` (`day_start_at`,`day_end_at`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='活动计划';

-- ----------------------------
-- Table structure for __PREFIX__xilufitness_work_course
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__xilufitness_work_course` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(30) unsigned DEFAULT '0' COMMENT '所属小程序',
  `course_id` int(30) DEFAULT NULL COMMENT '所属课程',
  `shop_id` int(30) unsigned DEFAULT '0' COMMENT '所属门店',
  `coach_id` int(30) unsigned DEFAULT '0' COMMENT '所属教练',
  `course_type` tinyint(2) unsigned DEFAULT '0' COMMENT '课程类型',
  `class_time` bigint(16) unsigned DEFAULT NULL COMMENT '上课日期',
  `start_at` bigint(16) DEFAULT NULL COMMENT '开始时间',
  `end_at` bigint(16) DEFAULT NULL COMMENT '结束时间',
  `course_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '课程价格',
  `write_off_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '核销收入',
  `market_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '市场价',
  `class_count` int(3) unsigned DEFAULT '1' COMMENT '课时数',
  `sign_count` int(5) DEFAULT NULL COMMENT '报名人数',
  `wait_count` int(5) DEFAULT NULL COMMENT '排队人数',
  `status` enum('normal','hidden','complete','failed') DEFAULT 'normal' COMMENT '状态',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `brand_shop_id` (`brand_id`,`shop_id`) USING BTREE,
  KEY `course_coach_id` (`course_id`,`coach_id`) USING BTREE,
  KEY `start_end_at` (`start_at`,`end_at`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='课程排课';

-- ----------------------------
-- Table structure for fitness_xilufitness_area
-- ----------------------------
CREATE TABLE `__PREFIX__xilufitness_area` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` int(10) DEFAULT NULL COMMENT '父id',
  `shortname` varchar(100) DEFAULT NULL COMMENT '简称',
  `name` varchar(100) DEFAULT NULL COMMENT '名称',
  `mergename` varchar(255) DEFAULT NULL COMMENT '全称',
  `level` tinyint(4) DEFAULT NULL COMMENT '层级 0 1 2 省市区县',
  `pinyin` varchar(100) DEFAULT NULL COMMENT '拼音',
  `code` varchar(100) DEFAULT NULL COMMENT '长途区号',
  `zip` varchar(100) DEFAULT NULL COMMENT '邮编',
  `first` varchar(50) DEFAULT NULL COMMENT '首字母',
  `lng` varchar(100) DEFAULT NULL COMMENT '经度',
  `lat` varchar(100) DEFAULT NULL COMMENT '纬度',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `pid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3749 DEFAULT CHARSET=utf8 COMMENT='全国城市表';

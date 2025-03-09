<template>
	<view class="xilu">
		<!-- <view class="page-foot bg-normal">
			<view class="p30">
				<view class="xilu_foot_btn1">查看签到码</view>
				<view class="xilu_foot_btn2">已签到</view>
			</view>
		</view> -->
		<view class="container">
			<view class="pr">
				<swiper class="xilu_swiper_info" @change="swiperTab" :current="current" :circular="true"
					:autoplay="true" :interval="3000" :duration="1000">
					<swiper-item v-for="(vo,index) in thumb_images">
						<image :src="vo" mode="aspectFill"></image>
					</swiper-item>
				</swiper>
				<view class="xilu_swiper_dot">{{ current + 1 }}/{{ thumb_images.length }}</view>
				<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_mask.png'" mode="aspectFill" class="xilu_mask"></image>
			</view>
			<view class="pr mt_132 z5 plr25 pb40">
				<view class="flex-box">
					<view class="col2 fs36 fw500 lh50 flex-grow-1">{{info.camp.title || ''}}</view>
					<view @click="user_invite()">
						<image src="@/static/images/xilu_icon9.png" mode="aspectFill" class="ico24"></image>
						<text class="ml10 fs28 colf">分享有礼</text>
					</view>
				</view>
				<view class="mt15 fs30 colf lh42" v-if="info && info.camp.lable_list.length > 0">
					{{info.camp.lable_list.join(' | ')}}</view>
				<view class="xilu_box" style="margin-top: 24rpx;padding-top: 20rpx;">
					<view class="bb pb20 fs30 colf lh42" v-if="info">
						最近开营：{{info.start_at || 0}}｜共{{info.current_camp_list.length || 0}}个活动报名中</view>
					<view class="pt20 fs30 col9 lh42" v-if="info.camp.description">{{info.camp.description || ''}}
					</view>
				</view>

				<view class="pr z2 mt30" v-if="info.shop" @tap="openLocation(info.shop.lat,info.shop.lng)">
					<image v-if="web_url" :src="web_url + '/uniapp_image/xilu_bg_info.png'" mode="aspectFill" class="xilu_bg_info"></image>
					<view class="xilu_bg_view flex-box plr30">
						<image src="@/static/images/xilu_icon_address_gray.png" mode="aspectFill" class="ico28 mb40">
						</image>
						<view class="flex-grow-1">
							<view class="w460 m-ellipsis-l2 mlr10 fs28 colf lh40">{{info.shop.address || ''}}</view>
						</view>
						<view class="tc">
							<image src="@/static/images/xilu_guide.png" mode="aspectFill" class="ico30"></image>
							<view class="fs24 col9 lh32 mt10">导航</view>
						</view>
					</view>
				</view>


				<view class="fs36 fw500 colf lh50 mt30">营期</view>
				<view class="xilu_info_item flex-box" v-for="(vo,keys) in info.current_camp_list" @tap="chooseCamp(vo)">
					<image :src="vo.camp.xilufitness_urls.thumb_image" mode="aspectFill" class="xilu_info_item_cover">
					</image>
					<view class="flex-grow-1 pl10">
						<view class="fs30 col2 fw500 lh42">{{vo.start_at || ''}}-{{vo.end_at || ''}}</view>
						<view class="flex flex-align-end">
							<view class="flex-grow-1 m-ellipsis fs24 colf lh34">
								<template v-for="(vv,key) in vo.plans">
									{{vv.week || ''}} {{vv.day_start_at || ''}}～{{vv.day_end_at || ''}}
								</template>
							</view>
							<view v-if="vo.user_signed == 0" class="xilu_btn">报名</view>
							<view v-else class="xilu_btn" style="background-color: #ccc;">已报名</view>
						</view>
						<view class="fs24 colf lh34 mt15 m-ellipsis">
							<text>¥{{info.camp_price || 0}}</text>
							<text class="col9 tdl fs20 ml5" v-if="info.market_price > 0">¥{{info.market_price || 0}}</text>
							<text class="xilu_textsq">{{info.class_count || 0}}课时</text>
							<text class="xilu_textsq">{{info.class_duration || 0}}分钟/课时</text>
						</view>
					</view>
				</view>


				<view class="xilu_box" style="padding-left: 28rpx;padding-right: 28rpx;" v-if="sign_list.list && sign_list.list.length > 0">
					<view class="colf fs36 fw500 lh50">本期报名（{{sign_list.user_count || 0}}人）</view>
					<view style="font-size: 0;">
						<image :src="vo.user.xilufitness_urls.avatar || '../../static/images/avatar.png' " mode="aspectFill" class="xilu_mini_head"
							v-for="(vo,index) in sign_list.list"></image>
					</view>
				</view>
				<view class="xilu_box" v-if="info.camp.content">
					<view class="pb30 mb30 fs36 fw500 colf lh50 bb">活动详情</view>
					<mp-html class="colf" :content="info.camp.content"></mp-html>
				</view>
				<view class="xilu_box" v-if="info.camp.tip_content">
					<view class="pb30 mb30 fs36 fw500 colf lh50 bb">注意事项</view>
					<mp-html class="colf" :content="info.camp.tip_content"></mp-html>
				</view>
				<u-authorize @onAuthCancel="onAuthCancel($event)" @onAuthConfirm="onAuthConfirm($event)"
					:popupStatus="auth_status" :isAuth="2"></u-authorize>
			</view>

			<u-popup :show="show" mode="bottom" @close="close" @open="open">
				<view class="xilu_popup pr pt30">
					<view class="tc fs34 colf pb30">{{camp_info.camp.title || ''}}</view>
					<image src="@/static/images/xilu_close.png" mode="aspectFill" class="xilu_popup_close"
						@tap="close()"></image>
					<scroll-view scroll-y="true" class="xilu_popup_scroll plr25">
						<view class="xilu_popup_box">
							<view class="flex-box mb40">
								<image src="@/static/images/xilu_icon10.png" mode="aspectFill" class="ico28"></image>
								<view class="flex-grow-1 pl10 fs28 colf">教练：{{camp_info.coach.coach_name || ''}}</view>
							</view>
							<view class="flex mb40">
								<image src="@/static/images/xilu_icon15.png" mode="aspectFill" class="ico28 mt6">
								</image>
								<view class="flex-grow-1 pl10 fs28 colf">日期：{{camp_info.start_at || ''}}-{{camp_info.end_at || ''}}
								</view>
							</view>

							<view class="flex mb40">
								<image src="@/static/images/xilu_icon16.png" mode="aspectFill" class="ico28 mt6">
								</image>
								<view class="flex-grow-1 pl10 fs28 colf">时长：{{camp_info.class_duration || 0}}分钟/课时</view>
							</view>
							<view class="flex mb40">
								<image src="@/static/images/xilu_icon12.png" mode="aspectFill" class="ico28 mt6">
								</image>
								<view class="flex-grow-1 pl10 fs28 colf">地址：{{camp_info.shop.address || ''}}</view>
							</view>
							<view class="flex">
								<image src="@/static/images/xilu_icon17.png" mode="aspectFill" class="ico28 mt6">
								</image>
								<view class="flex-grow-1 pl10 fs28 colf">
									人数：{{camp_info.camp_count || 0}}人开营，{{camp_info.total_count || 0}}人满员</view>
							</view>

						</view>

						<view class="xilu_popup_box" style="padding: 30rpx;">
							<view class="fs36 fw500 colf lh50">训练计划</view>
							<view class="xilu_popup_box_label" v-for="(vo,keys) in camp_info.plans">{{vo.week}}
								{{vo.day_start_at || ''}}～{{vo.day_end_at || ''}}</view>
						</view>
						<view class="xilu_popup_box" style="padding: 30rpx;" v-if="camp_info.description">
							<view class="fs32 fw500 colf lh46">其他说明：</view>
							<view class="pt60 fs28 col9 lh40">{{camp_info.description || ''}}</view>
						</view>
					</scroll-view>
					<view class="plr25 ptb30">
						<view v-if="camp_info.user_signed == 0" @click.stop="campSign(camp_info)" class="btn1">报名</view>
						<view v-else class="btn1" style="background-color: #ccc;">已报名</view>
					</view>
				</view>
			</u-popup>

		</view>
		<u-popup :show="showAttendance" mode="center" bgColor="transparent" @open="attendanceOpen"
			@close="attendanceClose" :safeAreaInsetBottom="false">
			<view class="xilu_code_popup pr pt40">
				<view class="tc fs34 col9 lh40">已签到<text class="col2">2</text>/10</view>
				<image src="@/static/images/xilu_close.png" mode="aspectFill" class="xilu_close"
					@click="attendanceClose"></image>
				<image src="@/static/logo.png" mode="aspectFill" class="xilu_code"></image>
				<view class="tc fs34 colf lh40">出示二维码核销</view>
			</view>
		</u-popup>
	</view>
</template>

<script>
	const app = getApp();
	const webConfig = require("@/util/config");
	export default {
		data() {
			return {
				current: 0,
				thumb_images: [],
				show: false,
				showAttendance: false,
				auth_status: false,
				info: null,
				camp_info: null,
				sign_list:[],
				web_url:''
			}
		},
		methods: {
			swiperTab(e) {
				this.current = Number(e.target.current)
				console.log(this.current);
			},
			open() {
				this.show = true
			},
			close() {
				this.show = false
			},
			attendanceOpen() {
				this.showAttendance = true
			},
			attendanceClose() {
				this.showAttendance = false
			},
			//选择活动
			chooseCamp(info) {
				this.show = true;
				this.camp_info = info || null;
			},
			// 获取活动详情
			getDetail(id) {
				let _this = this;
				this.$http({
					url: 'addons/xilufitness/course/getCampDetail',
					data: {
						id: id
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.info = res.data.info;
						_this.thumb_images = res.data.info.camp.xilufitness_urls.thumb_images || [];
						_this.sign_list = res.data.userList || [];
					}
				}).catch(error => {
					console.log('campdetailError', error);
				})
			},
			//打开地图
			openLocation(lat, lng) {
				this.$api.openLocation(lat, lng);
			},
			//活动报名
			campSign(camp_info) {
				let _this = this;
				let token = this.$api.getCache('token');
				if (!token) {
					this.show = false;
					this.auth_status = true;
				} else {
					this.$api.navigate('../pay_order/pay_order?id=' + camp_info.id + '&is_type=3');
				}
			},
			//授权取消
			onAuthCancel(e) {
				this.auth_status = false;
				this.$api.toast('取消授权，可能会使部分服务不能用，或页面信息不完整')
			},
			//授权成功
			onAuthConfirm(e) {
				this.auth_status = false;
				this.campSign(this.camp_info);
			},
			//邀请有礼
			user_invite() {
				let token = this.$api.getCache('token');
				if (!token) {
					this.auth_status = true;
				} else {
					this.$api.navigate('../invite/invite')
				}
				
			}
		},
		onLoad(options) {
			this.web_url = webConfig.base_url || '';
			this.getDetail(options.id || 0);
		},
		onShareAppMessage() {
		
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_swiper_info {
			width: 100%;
			height: 750rpx;
			position: relative;

			image {
				width: 100%;
				height: 100%;
				display: block;
			}
		}

		&_mask {
			width: 100%;
			height: 380rpx;
			position: absolute;
			bottom: 0;
			z-index: 3;
			left: 0;
		}

		&_swiper_dot {
			display: inline-block;
			height: 39rpx;
			line-height: 39rpx;
			background: rgba(0, 0, 0, 0.25);
			border-radius: 20rpx;
			position: absolute;
			font-size: 24rpx;
			font-weight: 400;
			color: #FFFFFF;
			right: 25rpx;
			bottom: 194rpx;
			padding-left: 15rpx;
			padding-right: 15rpx;
			z-index: 4;
		}

		&_box {
			background: #292B2C;
			border-radius: 20rpx;
			width: 100%;
			margin-top: 30rpx;
			padding: 30rpx;
		}

		&_bg_info {
			width: 700rpx;
			height: 140rpx;
			position: relative;
			z-index: 1;
		}

		&_bg_view {
			width: 700rpx;
			height: 140rpx;
			position: absolute;
			z-index: 2;
			top: 0;
			left: 0;
		}

		&_info_item {
			width: 700rpx;
			padding: 20rpx;
			background: #404243;
			border-radius: 20rpx;
			margin-top: 30rpx;

			&_cover {
				width: 150rpx;
				height: 150rpx;
				border-radius: 20rpx;
				display: block;
			}
		}

		&_btn {
			width: 118rpx;
			height: 57rpx;
			line-height: 57rpx;
			text-align: center;
			font-size: 28rpx;
			font-weight: 400;
			color: #0E0E0F;
			background: #FFCF00;
			border-radius: 10rpx;
		}

		&_textsq::before {
			content: '|';
			display: inline-block;
			vertical-align: middle;
			font-size: 24rpx;
			color: #999999;
			margin: 0 5rpx;
		}

		&_mini_head {
			width: 90rpx;
			height: 90rpx;
			border: 2rpx solid #FFFFFF;
			position: relative;
			margin-top: 30rpx;
			margin-left: -12rpx;
			display: inline-block;
			vertical-align: top;
			border-radius: 50%;

			&:first-child,
			&:nth-of-type(9n) {
				margin-left: 0;
			}
		}

		&_popup {
			width: 100%;
			background: #292B2C;

			&_scroll {
				max-height: 60vh;
				width: 100%;
			}

			&_close {
				width: 30rpx;
				height: 30rpx;
				top: 39rpx;
				right: 25rpx;
				position: absolute;
			}

			&_box+&_box {
				margin-top: 30rpx;
			}

			&_box {
				background: #404243;
				border-radius: 20rpx;
				padding: 30rpx 8rpx 30rpx 30rpx;
			}

			&_box_label {
				font-size: 28rpx;
				font-weight: 400;
				color: #FFFFFF;
				line-height: 40rpx;
				margin-top: 30rpx;

				&:before {
					width: 10rpx;
					height: 10rpx;
					background: #999999;
					content: '';
					display: inline-block;
					vertical-align: middle;
					border-radius: 50%;
					margin-right: 20rpx;
				}
			}

			&_box_label:first-child {
				margin-top: 20rpx;
			}
		}

		&_foot_btn1 {
			width: 690rpx;
			height: 90rpx;
			line-height: 90rpx;
			text-align: center;
			font-size: 30rpx;
			font-weight: 400;
			color: #0E0E0F;
			background: #FFCF00;
			border-radius: 20rpx;
		}

		&_foot_btn2 {
			width: 690rpx;
			height: 90rpx;
			line-height: 90rpx;
			text-align: center;
			font-size: 30rpx;
			font-weight: 400;
			color: #0E0E0F;
			background: #999999;
			border-radius: 20rpx;
		}

		&_code_popup {
			width: 600rpx;
			background: #404243;
			border-radius: 20rpx;
			border: 1px solid #454545;
			padding: 40rpx 0 50rpx;
		}

		&_close {
			width: 30rpx;
			height: 30rpx;
			position: absolute;
			top: 45rpx;
			right: 47rpx;
		}

		&_code {
			width: 405rpx;
			height: 400rpx;
			display: block;
			margin: 40rpx auto;
		}
	}

	.mt_132 {
		margin-top: -132rpx;
	}
</style>
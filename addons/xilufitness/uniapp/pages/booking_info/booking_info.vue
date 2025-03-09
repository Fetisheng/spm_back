<template>
	<view class="xilu">
		<view class="page-foot bg-normal">
			<view class="p30">
				<view v-if="info.order_status == 1 || info.order_status == 2" @click="codeOpen()" class="xilu_foot_btn1">查看签到码</view>
				<view v-if="info.order_status == 3 " @click="to_comment(info.id)" class="xilu_foot_btn1">去评价</view>
				<view class="xilu_foot_btn2" v-if="info.order_status == 5 ">已过期</view>
				<view class="xilu_foot_btn2" v-if="info.order_status == 6 ">课时已完成</view>
			</view>
		</view>
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
			<view class="pr mt_132 z5 plr25 pb30">
				<view class="flex-box">
					<view class="col2 fs36 fw500 lh50 flex-grow-1" v-if="info">
						{{info.course_camp.course.title || info.course_camp.camp.title}}
					</view>
					<view>
						<image @click="returnTips()" src="@/static/images/xilu_icon18.png" mode="aspectFill"
							class="ico24"></image>
						<text @click="returnTips()" class="ml10 fs28 col2">退款须知</text>
					</view>
				</view>
				<view class="pr mt30 xilu_lession_info">
					<image v-if="web_url" :src="web_url + '/uniapp_image/xilu_bg_info_big.png'" mode="aspectFill" class="xilu_lession_info_bg">
					</image>
					<view class="xilu_lession_info_view p30">
						<view class="flex-box">
							<image src="@/static/images/xilu_icon_time.png" mode="aspectFill" class="ico28 mid"></image>
							<view class="flex-grow-1 fs28 colf lh40 pl10">{{info.course_camp.class_time_txt || ''}}
								{{info.course_camp.start_at || ''}}–{{info.course_camp.end_at || ''}}
							</view>
						</view>
						<view class="mt40 flex"
							@tap="openLocation(info.course_camp.shop.lat,info.course_camp.shop.lng)">
							<image src="@/static/images/xilu_icon_address_gray.png" mode="aspectFill" class="ico28 mt8">
							</image>
							<view class="flex-grow-1 plr10">
								<view class="w460 fs28 colf lh40 m-ellipsis-l2">{{info.course_camp.shop.address || ''}}
								</view>
							</view>
							<view class="tc">
								<image src="@/static/images/xilu_guide.png" mode="aspectFill" class="ico30 mt5"></image>
								<view class="col9 fs24 mt10 lh34">导航</view>
							</view>
						</view>
					</view>
				</view>
				<view class="xilu_box flex-box" v-if="info">
					<image :src="info.course_camp.coach.xilufitness_urls.coach_avatar" mode="aspectFill"
						class="xilu_head_big"></image>
					<view class="flex-grow-1 pl30">
						<view class="fs40 fw500 lh56 colf">{{info.course_camp.coach.coach_name || ''}}</view>
						<view class="mt30 fs28 col9 lh40" v-if="info.course_camp.coach.lable_list.length > 0">
							{{info.course_camp.coach.lable_list.join('·')}}
						</view>
					</view>
				</view>
				<view class="xilu_box" style="padding-left: 28rpx;padding-right: 28rpx;"
					v-if="sign_list.list && sign_list.list.length > 0">
					<view class="colf fs36 fw500 lh50">本期报名（{{sign_list.user_count || 0}}人）</view>
					<view style="font-size: 0;">
						<image :src="vo.user.xilufitness_urls.avatar || '../../static/images/avatar.png' "
							mode="aspectFill" class="xilu_mini_head" v-for="(vo,index) in sign_list.list"></image>
					</view>
				</view>
				<template v-if="info.order_type == 3">
					<view class="xilu_box" v-if="info.course_camp.camp.content">
						<view class="pb30 mb30 fs36 fw500 colf lh50 bb">课程介绍</view>
						<view class="colf">
							<mp-html :content="info.course_camp.camp.content"></mp-html>
						</view>
					</view>
					<view class="xilu_box" v-if="info.course_camp.camp.tip_content">
						<view class="pb30 mb30 fs36 fw500 colf lh50 bb">注意事项</view>
						<view class="colf">
							<mp-html :content="info.course_camp.camp.tip_content"></mp-html>
						</view>
					</view>
				</template>

				<template v-else>
					<view class="xilu_box" v-if="info.course_camp.course.content">
						<view class="pb30 mb30 fs36 fw500 colf lh50 bb">课程介绍</view>
						<view class="colf">
							<mp-html :content="info.course_camp.course.content"></mp-html>
						</view>
					</view>
					<view class="xilu_box" v-if="info.course_camp.course.tip_content">
						<view class="pb30 mb30 fs36 fw500 colf lh50 bb">注意事项</view>
						<view class="colf">
							<mp-html :content="info.course_camp.course.tip_content"></mp-html>
						</view>
					</view>
				</template>

			</view>
		</view>
		<u-popup :show="showCode" mode="center" bgColor="transparent" @open="codeOpen" @close="codeClose"
			:safeAreaInsetBottom="false">
			<view class="xilu_code_popup pr pt40">
				<view class="tc fs34 col9 lh40">签到<text
						class="col2">{{info.code_num || 0}}</text>/{{info.code_total_num || 1}}</view>
				<image @click="codeClose()" src="@/static/images/xilu_close.png" mode="aspectFill" class="xilu_close">
				</image>

				<image :src="info.code_url" mode="aspectFill" class="xilu_code"></image>
				<view class="tc fs34 colf lh40">出示二维码核销</view>
			</view>
		</u-popup>
		<u-popup :show="showTips" mode="center" bgColor="transparent" @open="tipsOpen" @close="tipsClose"
			:safeAreaInsetBottom="false">
			<view class="xilu_code_popup pr pt40" style="width: 680rpx;">
				<view class="tc fs34 col9 lh40">退款须知</view>
				<image @tap="tipsClose()" src="@/static/images/xilu_close.png" mode="aspectFill" class="xilu_close">
				</image>
				<view class="plr20 fs28 colf lh40 mt40">
					<mp-html :content="tips_content"></mp-html>
				</view>
			</view>
		</u-popup>
		<!-- 	<u-popup :show="showAttendance" mode="center" bgColor="transparent" @open="attendanceOpen"
			@close="attendanceClose" :safeAreaInsetBottom="false">
			<view class="xilu_code_popup pr pt40">
				<view class="tc fs34 col9 lh40">已签到<text class="col2">2</text>/10</view>
				<image src="@/static/images/xilu_close.png" mode="aspectFill" class="xilu_close"></image>
				<image src="@/static/logo.png" mode="aspectFill" class="xilu_code"></image>
				<view class="tc fs34 colf lh40">出示二维码核销</view>
			</view>
		</u-popup> -->
	</view>
</template>

<script>
	const app = getApp();
	const webConfig = require("@/util/config");
	export default {
		data() {
			return {
				current: 0,
				showCode: false,
				showTips: false,
				showAttendance: false,
				info: '',
				thumb_images: [],
				sign_list: [],
				codeList: [],
				tips_content: '',
				web_url:''
			}
		},
		methods: {
			swiperTab(e) {
				this.current = Number(e.target.current)
				console.log(this.current);
			},
			codeOpen() {
				this.showCode = true
			},
			codeClose() {
				this.showCode = false
			},
			tipsOpen() {
				this.showTips = true
			},
			tipsClose() {
				this.showTips = false
			},
			attendanceOpen() {
				this.showAttendance = true
			},
			attendanceClose() {
				this.showAttendance = false
			},
			//去评价
			to_comment(id) {
				let _this = this;
				this.$api.navigate('../white_appraise/white_appraise?id=' + id,function(res){
					res.eventChannel.on('reloadOrder',function(){
						_this.getDetail(_this.info.id || 0);
					});
				});
			},
			//打开退款弹窗
			returnTips() {
				this.showTips = true;
			},
			//打开地图
			openLocation(lat, lng) {
				this.$api.openLocation(lat, lng);
			},
			//订单详情
			getDetail(id) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/order/getDetail',
					data: {
						id: id
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.info = res.data.info;
						
						if (res.data.info.order_type == 3) {
							_this.thumb_images = res.data.info.course_camp.camp.xilufitness_urls.thumb_images ||
						[];
						} else {
							_this.thumb_images = res.data.info.course_camp.course.xilufitness_urls.thumb_images ||
								[];
						}
						_this.sign_list = res.data.userList || [];
						_this.codeList = res.data.codeList || [];
					}
				}).catch(error => {
					console.log('orderDetailError', error);
				})
			},
			//退款须知
			getTips() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/cms/detail',
					data: {
						is_type: 4
					},
					method: 'get'
				}).then(res => {
					if(res.code == 1){
						_this.tips_content = (res.data.info && res.data.info.content) ? res.data.info.content : '';
					}
				}).catch(error => {
					console.log('cmsError', error);
				});
			}
		},
		onLoad(options) {
			this.web_url = webConfig.base_url || '';
			this.getDetail(options.id || 0);
			this.getTips();
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
			right: 30rpx;
			bottom: 194rpx;
			padding-left: 15rpx;
			padding-right: 15rpx;
			z-index: 4;
		}

		&_lession_info {
			width: 700rpx;
			height: 220rpx;
			z-index: 1;

			&_bg {
				width: 700rpx;
				height: 220rpx;
				position: relative;
				z-index: 1;
			}

			&_view {
				width: 700rpx;
				height: 220rpx;
				position: absolute;
				z-index: 1;
				left: 0;
				top: 0;
			}
		}

		&_box {
			background: #292B2C;
			border-radius: 20rpx;
			width: 100%;
			margin-top: 30rpx;
			padding: 30rpx;
		}

		&_head_big {
			width: 130rpx;
			height: 130rpx;
			border-radius: 50%;
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

		&_btn_more {
			font-size: 22rpx;
			font-weight: 400;
			color: #999999;
			width: 146rpx;
			height: 55rpx;
			line-height: calc(55rpx - 2px);
			text-align: center;
			border-radius: 28rpx;
			border: 1px solid #979797;
		}

		&_pj_item {

			border-top: 1px solid #434343;
			padding-top: 20rpx;
			padding-bottom: 20rpx;

			&_head_img {
				width: 75rpx;
				height: 75rpx;
				border-radius: 50%;
			}

			&_star {
				width: 23rpx;
				height: 21rpx;
			}
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
	}

	.mt_132 {
		margin-top: -132rpx;
	}

	.z5 {
		z-index: 5;
	}

	.bb {
		border-bottom: 1px solid #434343;
	}
</style>
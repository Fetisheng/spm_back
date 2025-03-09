<template>
	<view class="xilu">
		<view class="page-foot bg-normal plr25">
			<template v-if="info.user_signed == 1">
				<view class="btn2 mb30">已报名</view>
			</template>
			<template v-else>
				<view @click.stop="lessonSign()" v-if="info.is_plan == 1" class="btn1 mb30">去预约（<text
						class="col7">{{info.order_count || 0}}</text>/{{info.sign_count || 0 }}）</view>
				<view @click.stop="lessonSign()" v-else-if="info.is_plan == 2" class="btn1 mb30">可排队</view>
				<view v-else-if="info.is_plan == 3" class="btn2 mb30">已开始</view>
				<view v-else-if="info.is_plan == 4" class="btn2 mb30">已结束</view>
			</template>

		</view>
		<view class="container">
			<view class="pr">
				<swiper class="xilu_swiper_info" @change="swiperTab" :current="current" :circular="true"
					:autoplay="true" :interval="3000" :duration="1000">
					<swiper-item v-for="(vo,index) in thumb_images">
						<image :src="vo" mode="aspectFill"></image>
					</swiper-item>
				</swiper>
				<view class="xilu_swiper_dot">{{ current + 1 }}/{{ thumb_images.length || 0}}
				</view>
				<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_mask.png'" mode="aspectFill" class="xilu_mask">
				</image>
			</view>
			<view class="pr mt_132 z5 plr25 pb30">
				<view class="flex-box">
					<view class="col2 fs36 fw500 lh50 flex-grow-1">{{info.course.title || ''}}</view>
					<view @click="user_invite()">
						<image src="@/static/images/xilu_icon9.png" mode="aspectFill" class="ico24"></image>
						<text class="ml10 fs28 colf">分享有礼</text>
					</view>
				</view>
				<view class="pr mt30 xilu_lession_info">
					<image v-if="web_url" :src="web_url + '/uniapp_image/xilu_bg_info_big.png'" mode="aspectFill"
						class="xilu_lession_info_bg">
					</image>
					<view class="xilu_lession_info_view p30">
						<view class="flex-box">
							<image src="@/static/images/xilu_icon_time.png" mode="aspectFill" class="ico28 mid"></image>
							<view class="flex-grow-1 fs28 colf lh40 pl10">{{info.class_time_txt || ''}}
								{{info.start_at || ''}}–{{info.end_at || ''}}
							</view>
						</view>
						<view class="mt40 flex" @tap="openLocation(info.shop.lat,info.shop.lng)">
							<image src="@/static/images/xilu_icon_address_gray.png" mode="aspectFill"
								class="ico28 mid mt8"></image>
							<view class="flex-grow-1 plr10">
								<view class="w460 fs28 colf lh40 m-ellipsis-l2">{{info.shop.address || ''}}</view>
							</view>
							<view class="tc">
								<image src="@/static/images/xilu_guide.png" mode="aspectFill" class="ico30 mt5"></image>
								<view class="col9 fs24 mt10 lh34">导航</view>
							</view>
						</view>
					</view>
				</view>
				<view class="xilu_box flex-box" v-if="coach_info"
					@click.stop="redirect_coach_info((coach_info.id || 0),(info.shop.id || 0))">
					<image :src="coach_info.xilufitness_urls.coach_avatar || '../../static/images/avatar.png'"
						mode="aspectFill" class="xilu_head_big"></image>
					<view class="flex-grow-1 pl30">
						<view class="fs40 fw500 lh56 colf">{{coach_info.coach_name || ''}}</view>
						<view class="mt30 fs28 col9 lh40">{{coach_info.lable_list.join('·')}}</view>
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
				<view class="xilu_box" style="padding-bottom: 0;">
					<view class="flex-box flex-between mb30">
						<view class="fs36 fw500 colf">用户评价</view>
						<view class="xilu_btn_more" @click="commentList(info.course_id,info.course_type)">更多评价</view>
					</view>

					<template v-if="comment_list.length > 0">

						<view class="xilu_pj_item" v-for="(vo,keys) in comment_list">
							<view class="flex-box">
								<image :src="vo.user.xilufitness_urls.avatar || '../../static/images/avatar.png' "
									mode="aspectFill" class="xilu_pj_item_head_img">
								</image>
								<view class="flex-grow-1 pl30">
									<view class="flex-box">
										<view class="flex-grow-1 m-ellipsis pr10 fs30 fw500 colf lh30">
											{{vo.user.nickname || ''}}
										</view>
										<view class="fs24 col89 lh24">{{vo.createtime_txt || ''}}</view>
									</view>
									<view class="mt10 fs24 col2 lh36">
										<image src="@/static/images/xilu_star.png" mode="aspectFill"
											class="xilu_pj_item_star mr10"></image>{{vo.star || 0}}分
									</view>
								</view>
							</view>
							<view class="fs30 col9 lh44 mt10">{{vo.content || ''}}</view>
						</view>
					</template>

					<template v-else>
						<empty-data :tips="'暂无评论'" :lineHeight="100"></empty-data>
					</template>


				</view>

				<view class="xilu_box" v-if="info.course.content">
					<view class="pb30 mb30 fs36 fw500 colf lh50 bb">课程介绍</view>
					<view style="color: #fff;">
						<mp-html class="mb30" :content="info.course.content"></mp-html>
					</view>
				</view>
				<view class="xilu_box" v-if="info.course.tip_content">
					<view class="pb30 mb30 fs36 fw500 colf lh50 bb">注意事项</view>
					<view style="color: #fff;">
						<mp-html :content="info.course.tip_content"></mp-html>
					</view>
				</view>
			</view>
		</view>
		<u-authorize @onAuthCancel="onAuthCancel($event)" @onAuthConfirm="onAuthConfirm($event)"
			:popupStatus="auth_status" :isAuth="2"></u-authorize>
	</view>
</template>

<script>
	const app = getApp();
	const webConfig = require("@/util/config");
	export default {
		data() {
			return {
				current: 0,
				swiperList: ['', '', '', '', '', '', ''],
				info: null,
				course_type: 1,
				coach_info: null,
				thumb_images: [],
				content: [],
				tip_content: [],
				sign_list: [],
				comment_list: [],
				auth_status:false,
				web_url: ''
			}
		},
		methods: {
			swiperTab(e) {
				this.current = Number(e.target.current)
				console.log(this.current);
			},
			//课程详情
			detail(id) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/course/detail',
					data: {
						id: id
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.info = res.data.info || '';
						_this.thumb_images = res.data.info.course.xilufitness_urls.thumb_images || [];
						_this.coach_info = res.data.info.coach;
						_this.sign_list = res.data.userList || [];
						_this.getComments((res.data.info.course_id || 0), (res.data.info.course_type || 1));
					}
				}).catch(error => {
					console.log('courseDetailError', error);
				})
			},
			//打开地图
			openLocation(lat, lng) {
				this.$api.openLocation(lat, lng);
			},
			//去预约
			lessonSign() {
				let token = this.$api.getCache('token');
				let info = this.info;
				if (!token) {
					this.show = false;
					this.auth_status = true;
					console.log('111',this.auth_status);
				} else {
					this.$api.navigate('../pay_order/pay_order?id=' + info.id + '&is_type=' + this.course_type);
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
			},
			//邀请有礼
			user_invite() {
				let token = this.$api.getCache('token');
				if (!token) {
					this.auth_status = true;
				} else {
					this.$api.navigate('../invite/invite');
				}
			},
			//教练详情
			redirect_coach_info(id, shop_id) {
				this.$api.navigate('../trainer_info/trainer_info?id=' + id + '&shop_id=' + shop_id);
			},
			//更多评论
			commentList(course_camp_id, course_type) {
				this.$api.navigate('../user_appraise_list/user_appraise_list?course_camp_id=' + course_camp_id +
					'&course_type=' + course_type);
			},
			//获取评论
			getComments(course_camp_id, course_type) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/course/getCommentList',
					data: {
						id: course_camp_id,
						course_type: course_type
					}
				}).then(res => {
					if (res.code == 1) {
						_this.comment_list = res.data.list || [];
					}
				});
			}

		},
		onLoad(options) {
			this.web_url = webConfig.base_url || '';
			this.course_type = options.is_type || 1;
			this.detail(options.id || 0);
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
<template>
	<view class="xilu">
		<view class="container">
			<view class="pr">
				<swiper class="xilu_swiper_info" @change="swiperTab" :current="current" :circular="true"
					:autoplay="true" :interval="3000" :duration="1000">
					<swiper-item v-for="(vo,index) in shop_images">
						<image :src="vo" mode="aspectFill"></image>
					</swiper-item>
				</swiper>
				<view class="xilu_swiper_dot">{{ current + 1 }}/{{shop_images.length || 0}}</view>
			</view>
			<view class="plr25 ptb30">
				<view class="flex-box">
					<view class="flex-grow-1">
						<view class="col2 fw500 fs36 lh50">{{info.shop_name || ''}}</view>
						<view class="mt15 flex-box">
							<u-rate active-color="#FFCF00" inactive-color="#cccccc" size="25rpx"
								:gutter="info.star || 0"></u-rate>
							<view class="col2 ml10 fs24 lh36">{{info.star || 0}}分</view>
						</view>
					</view>
					<view @tap="collectShop(info.id)">
						<image :src="'../../static/images/xilu_fav_'+(info.is_collect == 0 ? 'u' : 's')+'c.png'"
							mode="aspectFill" class="xilu_fav"></image>
						<view class="col9 fs28 lh40 mt10">收藏</view>
					</view>
					
				</view>
				<view class="pr z2 mt20">
					<image v-if="web_url" :src="web_url + '/uniapp_image/xilu_bg_info.png'" mode="aspectFill" class="xilu_bg_info"></image>
					<view class="xilu_bg_view flex-box plr30" @click="openLocation(info.lat,info.lng)">
						<image src="@/static/images/xilu_icon_address_gray.png" mode="aspectFill" class="ico28 mb40">
						</image>
						<view class="flex-grow-1">
							<view class="w460 m-ellipsis-l2 mlr10 fs28 colf lh40">{{info.address || ''}}</view>
						</view>
						<view class="tc">
							<image src="@/static/images/xilu_guide.png" mode="aspectFill" class="ico30"></image>
							<view class="fs24 col9 lh32 mt10">导航</view>
						</view>
					</view>
				</view>
				<view class="fs36 fw500 colf lh50 mt30">团课</view>
				<view class="pt10">
					<template v-if="course_list.length > 0">
						<course-list :list="course_list"></course-list>
					</template>

					<template v-else>
						<empty-data :tips="'暂无课程'" :lineHeight="100"></empty-data>
					</template>

				</view>
				<view class="fs36 fw500 colf lh50 mt30">私教</view>
				<view class="xilu_box1 mt30">
					<template v-if="coach_list.length > 0">

						<view class="xilu_box1_item flex-box" v-for="(vo,keys) in coach_list" @click="lesson_detail(vo.id,2)">
							<image :src="vo.coach.xilufitness_urls.coach_avatar" mode="aspectFill"
								class="xilu_box1_cover">
							</image>
							<view class="flex-grow-1 plr20">
								<view class="fs30 fw500 colf">{{vo.coach.coach_name || ''}}<text class="xilu_tips"
										v-if="vo.coach.group_name">{{vo.coach.group_name || ''}}</text></view>
								<view class="mt25 fs30 col9 lh42 m-ellipsis" v-if="vo.coach.lable_list.length > 0">
									{{vo.coach.lable_list.join(' | ')}}
								</view>
							</view>
							<view class="xilu_btn">预约</view>
						</view>

					</template>

					<template v-else>
						<empty-data :tips="'暂无课程'" :lineHeight="100"></empty-data>
					</template>

				</view>
				<view class="fs36 fw500 colf lh50 mt30">活动</view>
				<template v-if="camp_list.length > 0">

					<view class="xilu_info_item flex-box" v-for="(vo,keys) in camp_list" @tap="camp_detail(vo.id)">
						<image :src="vo.camp.xilufitness_urls.thumb_image" mode="aspectFill"
							class="xilu_info_item_cover">
						</image>
						<view class="flex-grow-1 pl10">
							<view class="fs30 col2 fw500 lh42">{{vo.start_at || ''}}-{{vo.end_at || ''}}</view>
							<view class="flex flex-align-end">
								<view class="flex-grow-1 m-ellipsis fs24 colf lh34">
									<template v-for="(vv,kk) in vo.plans">
										{{vv.week || ''}}{{vv.day_start_at || ''}}～{{vv.day_end_at || ''}};
									</template>
								</view>
								<view  class="xilu_btn">报名</view>
							</view>
							<view class="fs24 colf lh34 mt15 m-ellipsis">
								<text>¥{{vo.camp_price}}</text>
								<text class="col9 tdl fs20 ml5" v-if="vo.market_price > 0">¥{{vo.market_price}}</text>
								<text class="xilu_textsq">{{vo.class_count || 0}}课时</text>
								<text class="xilu_textsq">{{vo.class_duration || 0}}分钟/课时</text>
							</view>
						</view>
					</view>

				</template>
				<template v-else>
					<empty-data :tips="'暂无活动'" :lineHeight="100"></empty-data>
				</template>

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
				auth_status: false,
				current: 0,
				info: null,
				shop_images: [],
				course_list: [],
				coach_list: [],
				camp_list: [],
				web_url:''
			}
		},
		methods: {
			swiperTab(e) {
				this.current = Number(e.target.current)
				console.log(this.current);
			},
			//门店详情
			getShopDetail(id) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/shop/detail',
					data: {
						id: id
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.info = res.data.info || null;
						_this.shop_images = res.data.info.xilufitness_urls.shop_images || [];
					}
				}).catch(error => {
					console.log('shopDetailError', error);
				})
			},
			//获取团课
			getCourseDetail(id) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/shop/getCourse',
					data: {
						id: id,
						is_type: 1,
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.course_list = res.data.list || [];
					}
				}).catch(error => {
					console.log('shopCourseError', error);
				})
			},
			//获取私教
			getCoachDetail(id) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/shop/getCourse',
					data: {
						id: id,
						is_type: 2,
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.coach_list = res.data.list || [];
					}
				}).catch(error => {
					console.log('shopCoachError', error);
				})
			},
			//获取活动
			getCampDetail(id) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/shop/getCamp',
					data: {
						id: id
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.camp_list = res.data.list || [];
					}
				}).catch(error => {
					console.log('shopCampError', error);
				})
			},
			//打开地图
			openLocation(lat, lng) {
				this.$api.openLocation(lat, lng);
			},
			//预约私教
			lesson_detail(id, is_type) {
				this.$api.navigate('../group_lessons_info/group_lessons_info?id=' + id + '&is_type=' + is_type);
			},
			//活动
			camp_detail(id) {
				this.$api.navigate('../bootcamp_info/bootcamp_info?id=' + id);
			},
			//收藏店铺
			collectShop(id) {
				let token = this.$api.getCache('token');
				if (!token) {
					this.auth_status = true;
				} else {
					let _this = this;
					this.$http({
						url: '/addons/xilufitness/user/collect',
						data: {
							id: id,
							is_type: 1
						},
						method: 'post'
					}).then(res => {
						if (res.code == 1) {
							_this.info.is_collect = res.data.is_collect;
						} else {
							this.$api.toast(res.msg || '收藏失败')
						}
					}).catch(error => {

					});
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
				this.collectShop(this.info.id || 0);
			}
		},
		onLoad(options) {
			this.web_url = webConfig.base_url || '';
			this.getShopDetail(options.id || 0);
			this.getCourseDetail(options.id || 0);
			this.getCoachDetail(options.id || 0);
			this.getCampDetail(options.id || 0);
		},
		onShareAppMessage() {
		
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_swiper_info {
			width: 100%;
			height: 590rpx;
			position: relative;

			image {
				width: 100%;
				height: 100%;
				display: block;
			}
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
			bottom: 34rpx;
			padding-left: 15rpx;
			padding-right: 15rpx;
		}

		&_fav {
			width: 60rpx;
			height: 60rpx;
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

		&_list_item:nth-of-type(2n) {
			margin-right: 0;
		}

		&_list_item {
			width: 340rpx;
			height: 545rpx;
			background: #292B2C;
			border-radius: 20rpx;
			margin-top: 20rpx;
			margin-right: 20rpx;
			display: inline-block;
			vertical-align: top;

			&_cover {
				width: 100%;
				height: 340rpx;
				display: block;
			}
		}

		&_box1 {
			width: 700rpx;
			background: #404243;
			border-radius: 20rpx;
			padding: 0 20rpx;

			&_item {
				padding-top: 20rpx;
				padding-bottom: 20rpx;

			}

			&_item+&_item {
				border-top: 1px solid #666;
			}

			&_cover {
				width: 130rpx;
				height: 130rpx;
				border-radius: 20rpx;
				display: block;
			}
		}

		&_tips {
			display: inline-block;
			height: 39rpx;
			line-height: 39rpx;
			background: linear-gradient(90deg, #FFD26A 0%, #FFB94E 100%);
			border-radius: 19rpx;
			padding: 0 15rpx;
			font-size: 24rpx;
			font-weight: 400;
			color: #000000;
			margin-left: 20rpx;
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
	}

	.mb46 {
		margin-bottom: 46rpx;
	}

	.w460 {
		width: 460rpx;
	}
</style>
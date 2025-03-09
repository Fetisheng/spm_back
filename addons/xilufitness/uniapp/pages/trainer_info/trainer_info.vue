<template>
	<view class="xilu">
		<view class="container">
			<view class="pr">
				<swiper class="xilu_swiper_info" @change="swiperTab" :current="current" :circular="true"
					:autoplay="true" :interval="3000" :duration="1000">
					<swiper-item v-for="(vo,index) in thumb_images">
						<image :src="vo" mode="aspectFill"></image>
					</swiper-item>
				</swiper>
				<view class="xilu_swiper_dot">{{ current + 1 }}/{{ thumb_images.length || 0}}</view>
				<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_mask.png'" mode="aspectFill" class="xilu_mask">
				</image>
			</view>
			<view class="pr z5 plr25 pb30">
				<view class="xilu_box">
					<view class="flex-box bb pb20">
						<view class="flex-grow-1">
							<view class="flex-box">
								<view class="fs44 fw500 colf lh62">{{info.coach_name || ''}}</view>
								<image :src="info.group_image" mode="widthFix" class="xilu_label ml20">
								</image>
							</view>
							<view class="mt30 fs30 colf lh42" v-if="lable_list.length > 0">{{lable_list.join('｜')}}
							</view>
						</view>
						<view class="tc" @tap="collect(info.id,info.shop.id)">
							<image :src="'../../static/images/xilu_fav_'+(info.is_collect == 1 ? 's' : 'u')+'c.png'"
								mode="aspectFill" class="ico60"></image>
							<view class="fs28 col9 lh40 mt10">收藏</view>
						</view>
					</view>
					<view class="mt20 fs30 col9 lh42">
						{{info.coach_info || ''}}
					</view>
				</view>

				<view class="pr z2 mt30" v-if="info.shop" @tap="openLocation(info.shop.lat,info.shop.lng)">
					<image v-if="web_url" :src="web_url + '/uniapp_image/xilu_bg_info.png'" mode="aspectFill"
						class="xilu_bg_info"></image>
					<view class="xilu_bg_view flex-box plr30">
						<image src="@/static/images/xilu_icon_address_gray.png" mode="aspectFill" class="ico28">
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

				<view class="xilu_scroll mt30">
					<view class="xilu_scroll_item" :class="course_type==1?'active':''"
						@click="chooseTab(1,info.id,info.shop.id)">团课
					</view>
					<view class="xilu_scroll_item" :class="course_type==2?'active':''"
						@click="chooseTab(2,info.id,info.shop.id)">私教
					</view>
					<view class="xilu_scroll_item" :class="course_type==3?'active':''"
						@click="chooseTab(3,info.id,info.shop.id)">活动
					</view>
				</view>


				<view class=" pt10" v-if="course_type==1">
					<template v-if="course_list.length > 0">
						<course-list :list="course_list"></course-list>
					</template>

					<template v-else>
						<empty-data :tips="'暂无课程'" :lineHeight="100"></empty-data>
					</template>

				</view>


				<view class=" pt10" v-if="course_type==2">

					<template v-if="course_list.length > 0">

						<view class="xilu_list_item" v-for="(vo,keys) in course_list" @tap="course_detail(vo.id,2)">
							<image :src="vo.course.xilufitness_urls.thumb_image" mode="aspectFill"
								class="xilu_list_item_cover">
							</image>
							<view class="pt10 plr20">
								<view class="m-ellipsis fs30 fw500 colf lh42">{{vo.course.title || ''}}</view>
								<view class="m-ellipsis mt10 fs24 col9 lh32"
									v-if="vo.course && vo.course.lable_list.length > 0">
									{{vo.course.lable_list.join('｜')}}
								</view>
								<view class="flex-box mt10">
									<view class="col2 fs24 flex-grow-1">{{vo.class_count || 0}}课时</view>
									<view class="m-ellipsis"><text
											class="col2 fs34 fw500 lh34 pr10">¥{{vo.course_price || 0}}</text><text
											class="tdl fs20 col9 lh34"
											v-if="vo.market_price > 0">¥{{vo.market_price || 0}}</text></view>
								</view>
							</view>
						</view>

					</template>

					<template v-else>
						<empty-data :tips="'暂无课程'" :lineHeight="100"></empty-data>
					</template>
				</view>

				<view v-if="course_type==3">
					<template v-if="course_list.length > 0">

						<view class="xilu_info_item flex-box" v-for="(vo,keys) in course_list"
							@tap="camp_detail(vo.id)">
							<image :src="vo.camp.xilufitness_urls.thumb_image" mode="aspectFill"
								class="xilu_info_item_cover">
							</image>
							<view class="flex-grow-1 pl10">
								<view class="fs30 col2 fw500 lh42">{{vo.start_at || ''}}-{{vo.end_at || ''}}</view>
								<view class="flex flex-align-end">
									<view class="flex-grow-1 m-ellipsis fs24 colf lh34"
										v-if="vo.plans && vo.plans.length > 0">
										<template v-for="(vvv,kkk) in vo.plans">
											{{vvv.week || ''}}{{vvv.day_start_at || ''}}～{{vvv.day_end_at || ''}}
										</template>
									</view>
									<view class="xilu_btn">报名</view>
								</view>
								<view class="fs24 colf lh34 mt15 m-ellipsis">
									<text>¥{{vo.camp_price || ''}}</text>
									<text class="col9 tdl fs20 ml5"
										v-if="vo.market_price > 0">¥{{vo.market_price || 0}}</text>
									<text class="xilu_textsq">{{vo.class_count || 0}}课时</text>
									<text class="xilu_textsq">{{vo.class_duration || ''}}分钟/课时</text>
								</view>
							</view>
						</view>

					</template>

					<template v-else>
						<empty-data :tips="'暂无活动'" :lineHeight="100"></empty-data>
					</template>

				</view>
				<view class="xilu_box" style="padding-bottom: 0;margin-top: 30rpx;">
					<view class="flex-box flex-between mb30">
						<view class="fs36 fw500 colf">用户评价</view>
						<view @click="commentList(shop_info.id || 0)" class="xilu_btn_more">更多评价</view>
					</view>

					<template v-if="comment_list.length > 0">
						<view class="xilu_pj_item" v-for="(vo,keys) in comment_list">
							<view class="flex-box">
								<image :src="vo.user.xilufitness_urls.avatar || '../../static/images/avatar.png'"
									mode="aspectFill" class="xilu_pj_item_head_img">
								</image>
								<view class="flex-grow-1 pl30">
									<view class="flex-box">
										<view class="flex-grow-1 m-ellipsis pr10 fs30 fw500 colf lh30">
											{{vo.user.nickname || ''}}</view>
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


			</view>
		</view>
		<u-authorize @onAuthCancel="onAuthCancel($event)" @onAuthConfirm="onAuthConfirm($event)"
			:popupStatus="auth_status" :isAuth="2"></u-authorize>
	</view>
</template>

<script>
	import {
		methods
	} from '../../uni_modules/uview-ui/libs/mixin/mixin';
	const app = getApp();
	const webConfig = require("@/util/config");
	export default {
		data() {
			return {
				current: 0,
				course_type: 1,
				thumb_images: [],
				info: null,
				lable_list: [],
				shop_info: null,
				course_list: [],
				auth_status: false,
				comment_list: [],
				web_url: ''
			}
		},
		methods: {
			swiperTab(e) {
				this.current = Number(e.target.current)
				console.log(this.current);
			},
			//授权取消
			onAuthCancel(e) {
				this.auth_status = false;
				this.$api.toast('取消授权，可能会使部分服务不能用，或页面信息不完整')
			},
			//授权成功
			onAuthConfirm(e) {
				this.auth_status = false;
				this.collect((this.info.id || 0), (this.info.shop.id || 0));
			},
			//选择课程类型
			chooseTab(is_type, id, shop_id) {
				this.course_type = is_type;
				this.getCourseList(id, shop_id, is_type);
			},
			//教练详情
			getDetail(id, shop_id) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/coach/detail',
					data: {
						id: id,
						shop_id: shop_id
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.info = res.data.info;
						_this.thumb_images = res.data.info.xilufitness_urls.coach_images || [];
						_this.shop_info = res.data.info.shop || null;
						_this.lable_list = res.data.info.lable_list || [];
					}
				}).catch(error => {
					console.log('coachError', error);
				})
			},
			//获取课程数据
			getCourseList(id, shop_id, is_type) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/coach/getList',
					data: {
						id: id,
						shop_id: shop_id,
						is_type: is_type
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.course_list = res.data.list;
					}
				}).catch(error => {
					console.log('courseError', error);
				})
			},
			//获取评论
			getCommentList(shop_id) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/course/getCommentList',
					data: {
						shop_id: shop_id,
						page: 1
					},
					methods: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.comment_list = res.data.list || [];
					}
				}).catch(error => {
					console.log('commentError', error);
				});
			},
			//打开地图
			openLocation(lat, lng) {
				this.$api.openLocation(lat, lng);
			},
			//课程详情
			course_detail(id, is_type) {
				this.$api.navigate('../group_lessons_info/group_lessons_info?id=' + id + '&is_type=' + is_type)
			},
			//活动详情
			camp_detail(id) {
				this.$api.navigate('../bootcamp_info/bootcamp_info?id=' + id);
			},
			// 收藏
			collect(id, shop_id) {
				let _this = this;
				let token = this.$api.getCache('token');
				if (!token) {
					this.auth_status = true;
				} else {
					this.$http({
						url: '/addons/xilufitness/user/collect',
						data: {
							id: id,
							is_type: 2,
							shop_id: shop_id
						},
						method: 'post'
					}).then(res => {
						if (res.code == 1) {
							_this.info.is_collect = res.data.is_collect
						}
					}).catch(error => {
						console.log('coachError', error);
					})
				}
			},
			//更多评论
			commentList(shop_id) {
				this.$api.navigate('../user_appraise_list/user_appraise_list?shop_id=' + shop_id);
			},
		},
		onLoad(options) {
			this.web_url = webConfig.base_url || '';
			this.getCommentList(options.shop_id || 0);
			this.getDetail((options.id || 0), (options.shop_id || 0));
			this.getCourseList((options.id || 0), (options.shop_id || 0), this.course_type);
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
			bottom: 220rpx;
			padding-left: 15rpx;
			padding-right: 15rpx;
			z-index: 4;
		}

		&_box {
			width: 700rpx;
			padding: 30rpx;
			margin-top: -190rpx;
			background: #292B2C;
			border-radius: 20rpx;
		}

		&_label {
			width: 161rpx;
			height: 38rpx;
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

		&_scroll {
			&_item {
				display: inline-block;
				vertical-align: top;
				font-size: 34rpx;
				font-weight: 500;
				color: #999999;
				line-height: 48rpx;
			}

			&_item.active {
				font-weight: 500;
				color: #FFFFFF;
			}

			&_item+&_item {
				margin-left: 40rpx;
			}
		}

		&_list_item:nth-of-type(2n) {
			margin-right: 0;
		}

		&_list_item {
			width: 340rpx;
			padding-bottom: 20rpx;
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
	}
</style>
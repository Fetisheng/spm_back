<template>
	<view class="xilu">
		<view class="container">
			<view class="p30">
				<view class="xilu_part1">
					<view class="m-ellipsis fs34 fw500 colf lh48" v-if="course_type == 3">{{info.camp.title || ''}}
					</view>
					<view class="m-ellipsis fs34 fw500 colf lh48" v-else>{{info.course.title || ''}}</view>
					<view class="mt30 flex-box">
						<image src="@/static/images/xilu_icon25.png" mode="aspectFill" class="ico28"></image>
						<view class="plr10 w222 fs28 colf" v-if="course_type == 3">
							{{info.plan.day_start_at || ''}}–{{info.plan.day_end_at || ''}}
						</view>
						<view class="plr10 w222 fs28 colf" v-else>{{info.start_at || ''}}–{{info.end_at || ''}}</view>
						<image src="@/static/images/xilu_icon26.png" mode="aspectFill" class="ico28"></image>
						<view class="fs28 colf plr10">总人数<text class="col2">{{user_count || 0}}</text></view>
					</view>
				</view>

				<view class="pr mt30 xilu_lession_info">
					<image v-if="web_url" :src="web_url + '/uniapp_image/xilu_bg_info_big.png'" mode="aspectFill" class="xilu_lession_info_bg">
					</image>
					<view class="xilu_lession_info_view p30">
						<view class="flex-box">
							<image src="@/static/images/xilu_icon_time.png" mode="aspectFill" class="ico28 mid"></image>
							<view class="flex-grow-1 fs28 colf lh40 pl10" v-if="course_type == 3">
								{{info.plan.day_date || ''}} {{info.plan.day_start_at || ''}}–{{info.plan.day_end_at || ''}}
							</view>
							<view class="flex-grow-1 fs28 colf lh40 pl10" v-else>{{info.class_time_txt || ''}}
								{{info.start_at || ''}}–{{info.end_at || ''}}
							</view>
						</view>
						<view class="mt40 flex" @tap="openLocation(info.shop.lat,info.shop.lng)">
							<image src="@/static/images/xilu_icon_address_gray.png" mode="aspectFill" class="ico28 mt8">
							</image>
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

				<view class="colf mt30 fs36 lh36">总人数（<text class="col2">{{user_count}}</text>）</view>
				<view class="pb30">
					<template v-if="userList.length > 0">
						<view class="xilu_user_item flex-box" v-for="(vo,index) in userList">
							<image :src="vo.user.xilufitness_urls.avatar || '../../static/images/avatar.png' "
								mode="aspectFill" class="xilu_user_item_cover"></image>
							<view class="flex-grow-1 pl20">
								<view class="fs30 fw500 colf lh36">{{vo.user.nickname || ''}}</view>
								<view class="mt20 fs28 col9 fw500 lh40">
									<text>累计天数{{vo.user.train_day || 0}}</text>
									<text class="pl30">训练次数{{vo.user.train_count || 0}}</text>
									<text class="pl30">训练时长{{vo.user.train_duration || 0}}</text>
								</view>
							</view>
						</view>
					</template>

					<template v-else>
						<empty-data :tips="'暂无报名记录'" :lineHeight="200"></empty-data>
					</template>

				</view>
			</view>
		</view>
	</view>
</template>

<script>
	const app = getApp();
	const webConfig = require("@/util/config");
	export default {
		data() {
			return {
				info: null,
				shop_info: null,
				userList: [],
				user_count: 0,
				course_type: 1,
				web_url:''
			}
		},
		methods: {
			//获取详情
			getDetail(id, work_camp_id, course_type) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/coach/getSchedulingDetail',
					data: {
						id: id,
						work_camp_id: work_camp_id,
						is_type: course_type
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.info = res.data.info;
						_this.shop_info = res.data.shop_info;
						_this.userList = res.data.userList || [];
						_this.course_type = course_type;
						_this.user_count = res.data.user_count;
					}
				}).catch(error => {
					console.log('schedulingDetailError', error);
				});
			}
		},
		onLoad(options) {
			this.web_url = webConfig.base_url || '';
			this.getDetail((options.id || 0), (options.work_camp_id), (options.is_type || 1));
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_part1 {
			width: 700rpx;
			height: 178rpx;
			background: #292B2C;
			border-radius: 20rpx;
			padding-left: 25rpx;
			padding-right: 25rpx;
			padding-top: 28rpx;
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

		&_user_item {
			width: 700rpx;
			height: 135rpx;
			background: #404243;
			border-radius: 20rpx;
			padding: 0 20rpx;
			margin-top: 30rpx;

			&_cover {
				width: 90rpx;
				height: 90rpx;
				border-radius: 50%;
			}
		}
	}

	.w222 {
		width: 222rpx;
	}
</style>
<template>
	<view class="xilu">
		<view class="page-foot bg-normal" @click="to_coupon()">
			<view class="pb40 plr25">
				<view class="btn1">兑换优惠劵</view>
			</view>
		</view>
		<view class="container">
			<view class="plr25 pt30 pb40">
				<view class="xilu_top_nav">
					<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_bg_booking.png'" mode="aspectFill" class="xilu_top_nav_bg"></image>
					<view class="xilu_top_nav_view">
						<view class="flex-box">
							<view class="flex-grow-1 fs28 colf lh40 m-ellipsis">我的积分</view>
							<image src="@/static/images/xilu_icon19.png" mode="aspectFill" class="ico30"></image>
							<view @click="to_coupon()" class="fs30 col2">获取积分</view>
						</view>
						<view class="mt15 fs70 fw500 lh98 colf">{{userInfo.point || 0}}</view>
					</view>
				</view>
				<view class="plr5">
					<view class="pt40 pb10 fs36 colf lh36">积分明细</view>
					<view>
						<template v-if="list.length > 0">
							<view class="xilu_item flex-box" v-for="(vo,keys) in list">
								<view class="flex-grow-1">
									<view class="fs32 colf lh44">{{vo.title || ''}}</view>
									<view class="mt20 fs28 col9 lh40">{{vo.createtime || ''}}</view>
								</view>
								<view class="col2 fs40">{{vo.point_type == 1 ? '+' : '-'}}{{vo.point || 0}}</view>
							</view>
						</template>

						<template v-else>
							<empty-data :tips="'暂无积分记录'" :lineHeight="300"></empty-data>
						</template>

					</view>

				</view>
			</view>
		</view>
	</view>
</template>

<script>
	var eventChannel = null;
	const webConfig = require("@/util/config");
	export default {
		data() {
			return {
				userInfo: null,
				list: [],
				page: 1,
				total_count: 0,
				web_url:''
			}
		},
		methods: {
			//获取积分列表
			getList() {
				let _this = this;
				_this.$http({
					url: '/addons/xilufitness/user/getMyPointList',
					data: {
						page: _this.page
					},
					method: 'get'
				}).then(res => {
					if (_this.page > 1) {
						_this.list.push(...res.data.list);
					} else {
						_this.list = res.data.list;
					}
					_this.total_count = res.data.total_count;
				}).catch(error => {
					console.log('pointListError', error);
				});
			},
			//跳转我的优惠券列表
			to_coupon(){
				this.$api.navigate('../my_coupons_list/my_coupons_list');
			}
		},
		onLoad() {
			let _this = this;
			this.web_url = webConfig.base_url || '';
			this.getList();
			eventChannel = this.getOpenerEventChannel();
			eventChannel.on('userData', function(params) {
				_this.userInfo = params.userInfo || null;
			});
		},
		onReachBottom() {
			let list = this.list;
			let total_count = this.total_count;
			if (list.length > total_count) {
				this.page = this.page + 1;
				this.getList();
			}
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_top_nav {
			width: 100%;
			height: 230rpx;
			position: relative;

			&_bg {
				width: 100%;
				height: 230rpx;
				position: relative;
				display: block;
				z-index: 1;
			}

			&_view {
				width: 100%;
				height: 230rpx;
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				z-index: 2;
				padding: 44rpx 30rpx 30rpx;
			}
		}

		&_item {
			width: 700rpx;
			height: 155rpx;
			background: #404243;
			border-radius: 20rpx;
			margin-top: 30rpx;
			padding: 0 30rpx;
		}
	}
</style>
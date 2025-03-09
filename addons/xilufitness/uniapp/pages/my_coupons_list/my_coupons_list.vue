<template>
	<view class="xilu">
		<view class="page-head bg-normal">
			<scroll-view scroll-x="true" class="xilu_top_tab">
				<view class="xilu_top_tab_item" :class="nindex==0?'active':''" @click="chooseNav(0)">待领取</view>
				<view class="xilu_top_tab_item" :class="nindex==1?'active':''" @click="chooseNav(1)">待使用</view>
				<view class="xilu_top_tab_item" :class="nindex==2?'active':''" @click="chooseNav(2)">已使用</view>
				<view class="xilu_top_tab_item" :class="nindex==3?'active':''" @click="chooseNav(3)">已过期</view>
			</scroll-view>
		</view>
		<view class="container" style="padding-top: 108rpx;">
			<view class="pt25 plr30" v-if="nindex==0">
				<template v-if="list.length > 0">
					<view class="xilu_item" v-for="(vo,index) in list">
						<image src="@/static/images/xilu_coupons.png" mode="aspectFill" class="xilu_item_bg"></image>
						<view class="xilu_item_view flex-box">
							<view class="flex-grow-1">
								<view class="fs32 fw500 col1 lh44 pl5 m-ellipsis">{{vo.title || ''}}</view>
								<view class="fs30 col89 lh50 mt10 pl5"><text
										class="fs50 col3 fwb mr10">¥{{vo.discount_amount || ''}}</text>满{{vo.meet_amount || ''}}可用
								</view>
								<view class="fs28 col89 lh40 mt10">有效期：{{vo.expire_time || ''}}(剩余{{vo.expire_day || ''}}天)</view>
							</view>
							<view @tap="receiveCoupon(vo.id)" class="xilu_item_btn">立即领取</view>
						</view>
					</view>
				</template>

				<template v-else>
					<empty-data :tips="'暂无优惠券'" :lineHeight="100"></empty-data>
				</template>

			</view>

			<view class="pt25 plr30" v-if="nindex==1">
				<template v-if="list.length > 0">
					<view class="xilu_item" v-for="(vo,index) in list" @tap="useCoupon()">
						<image src="@/static/images/xilu_coupons.png" mode="aspectFill" class="xilu_item_bg"></image>
						<view class="xilu_item_view flex-box">
							<view class="flex-grow-1">
								<view class="fs32 fw500 col1 lh44 pl5 m-ellipsis">{{vo.title || ''}}</view>
								<view class="fs30 col89 lh50 mt10 pl5"><text
										class="fs50 col3 fwb mr10">¥{{vo.discount_amount || ''}}</text>满{{vo.meet_amount || ''}}可用
								</view>
								<view class="fs28 col89 lh40 mt10">
									有效期：{{vo.expire_time_txt || ''}}(剩余{{vo.expire_day || 0}}天)</view>
							</view>
							<view class="xilu_item_btn">立即使用</view>
						</view>
					</view>
				</template>
				<template v-else>
					<empty-data :tips="'暂无优惠券'" :lineHeight="100"></empty-data>
				</template>

			</view>

			<view class="pt25 plr30" v-if="nindex==2">
				<template v-if="list.length > 0">
					<view class="xilu_item" v-for="(vo,index) in list">
						<image src="@/static/images/xilu_coupons_used.png" mode="aspectFill" class="xilu_item_bg">
						</image>
						<view class="xilu_item_view flex-box">
							<view class="flex-grow-1">
								<view class="fs32 fw500 col1 lh44 pl5 m-ellipsis">{{vo.title || ''}}</view>
								<view class="fs30 col89 lh50 mt10 pl5"><text
										class="fs50 col3 fwb mr10">¥{{vo.discount_amount || ''}}</text>满{{vo.meet_amount || ''}}可用
								</view>
								<view class="fs28 col89 lh40 mt10">有效期：{{vo.expire_time_txt || ''}}</view>
							</view>
							<image src="@/static/images/xilu_used.png" mode="aspectFill" class="ico110"></image>
						</view>
					</view>

				</template>
				<template v-else>
					<empty-data :tips="'暂无优惠券'" :lineHeight="100"></empty-data>
				</template>
			</view>
			<view class="pt25 plr30" v-if="nindex==3">
				<template v-if="list.length > 0">
					<view class="xilu_item" v-for="(vo,index) in list">
						<image src="@/static/images/xilu_coupons_used.png" mode="aspectFill" class="xilu_item_bg">
						</image>
						<view class="xilu_item_view flex-box">
							<view class="flex-grow-1">
								<view class="fs32 fw500 col1 lh44 pl5 m-ellipsis">{{vo.title || ''}}</view>
								<view class="fs30 col89 lh50 mt10 pl5"><text
										class="fs50 col3 fwb mr10">¥{{vo.discount_amount || ''}}</text>满{{vo.meet_amount || ''}}可用
								</view>
								<view class="fs28 col89 lh40 mt10">有效期：{{vo.expire_time_txt || ''}}</view>
							</view>
							<image src="@/static/images/xilu_timeout.png" mode="aspectFill" class="ico110"></image>
						</view>
					</view>
				</template>
				<template v-else>
					<empty-data :tips="'暂无优惠券'" :lineHeight="100"></empty-data>
				</template>

			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				nindex: 0,
				list: [],
			}
		},
		methods: {
			chooseNav(e) {
				this.nindex = e;
				this.clearData();
			},
			//清楚数据
			clearData() {
				this.list = [];
				this.getLists();
			},
			//优惠券列表
			getLists() {
				let _this = this;
				let url = this.nindex == 0 ? '/addons/xilufitness/coupon/getList' :
					'/addons/xilufitness/coupon/getMyCoupon';
				this.$http({
					url: url,
					data: {
						status: _this.nindex
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.list = res.data.list;
					}
				}).catch(error => {
					console.log('couponError', error);
				})
			},
			//领取优惠券
			receiveCoupon(id) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/coupon/getCoupon',
					data: {
						id: id
					},
					method: 'post'
				}).then(res => {
					if (res.code == 1) {
						_this.$api.toast('领取成功')
						_this.getLists();
					} else {
						_this.$api.modal('温馨提示', (res.msg || '领取失败'), function(res) {

						}, false)
					}
				}).catch(error => {
					console.log('receiveCouponError',error);
				})
			},
			//使用优惠券
			useCoupon(){
				this.$api.reLaunch("../course_classification/course_classification");
			}
		},
		onLoad() {
			let token = this.$api.getCache('token');
			if (!token) {
				this.$api.toast('请先登录')
				this.$api.back(2000);
			} else {
				this.getLists();
			}
		},
		onShareAppMessage() {
		
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_top_tab {
			height: 108rpx;
			line-height: 108rpx;
			white-space: nowrap;
			padding-left: 50rpx;
			padding-right: 50rpx;

			&_item {
				margin-right: 80rpx;
				font-size: 34rpx;
				font-family: PingFangSC, PingFang SC;
				font-weight: 400;
				color: #999999;
				position: relative;
				height: 108rpx;
				line-height: 108rpx;
				display: inline-block;
				vertical-align: top;
			}

			&_item.active {
				font-weight: 500;
				color: #FFFFFF;
			}

			&_item.active::after {
				content: '';
				width: 24rpx;
				height: 6rpx;
				background: #FFCF00;
				border-radius: 33rpx;
				bottom: 7rpx;
				left: 50%;
				transform: translateX(-50%);
				position: absolute;
			}
		}

		&_item {
			position: relative;
			width: 690rpx;
			height: 200rpx;
			margin-bottom: 30rpx;

			&_bg {
				position: relative;
				width: 690rpx;
				height: 200rpx;
				z-index: 1;
			}

			&_view {
				position: absolute;
				width: 690rpx;
				height: 200rpx;
				top: 0;
				left: 0;
				right: 0;
				z-index: 2;
				padding: 0 34rpx 0 25rpx;
			}

			&_btn {
				width: 132rpx;
				height: 60rpx;
				line-height: 60rpx;
				text-align: center;
				background: #0E0E0F;
				border-radius: 10rpx;
				font-size: 28rpx;
				font-weight: 400;
				color: #FFCF00;
			}
		}
	}

	.ico110 {
		width: 110rpx;
		height: 110rpx;
	}
</style>
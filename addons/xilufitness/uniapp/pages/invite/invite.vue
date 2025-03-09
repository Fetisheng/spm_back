<template>
	<view class="xilu">
		<view class="page-foot bg-normal">
			<view class="plr25 pb35">
				<view v-if="!login_status" @click="share_friend()" class="btn1">邀请好友</view>
				<button v-else class="btn1" open-type="share">邀请好友</button>
			</view>
		</view>
		<view class="container">
			<image v-if="web_url" :src="web_url + '/uniapp_image/xilu_invite.png'" mode="aspectFill" class="xilu_bg">
			</image>
			<view class="xilu_content_nav ml30 pb40">
				<view class="xilu_content_nav_box">
					<view class="fs36 fw500 colf lh36">邀请奖励</view>
					<view>
						<template v-if="list.length > 0">
							<view class="xilu_coupons mt30" v-for="(vo,index) in list">
								<image src="@/static/images/xilu_coupons_invite.png" mode="aspectFill"
									class="xilu_coupons_bg"></image>
								<view class="xilu_coupons_view pt17">
									<view class="tc col0e fwb lh104"><text class="fs30">¥</text><text
											class="fs104">{{vo.discount_amount || 0}}</text></view>
									<view class="fs14 col0e lh20 tc mt_20">·VOUCHER·</view>
									<view class="xilu_sq"></view>
									<view class="mt10 fs22 col0e lh30 tc">{{vo.title || ''}}</view>
									<view class="xilu_coupons_btn normal" v-if="vo.share_count != vo.invite_num">
										邀{{vo.invite_num || 0}}人可领</view>
									<view class="xilu_coupons_btn" @click="receiveCoupon(vo.id)"
										v-else-if="vo.is_receive == 0">立即领取</view>
									<view class="xilu_coupons_btn" v-else-if="vo.is_receive == 1">已领取</view>
								</view>
							</view>
						</template>
						<template v-else>
							<empty-data :tips="'暂无优惠券'" :lineHeight="100"></empty-data>
						</template>
					</view>

					<view class="xilu_progress_nav">
						<view class="xilu_progress_nav_active" :style="{'width':width+'%'}"></view>
					</view>
					<view class="mt30 tc col9 fs30 lh36">邀请<text
							class="col2">{{rec_count || 0}}</text>/{{total_count || 0}}人</view>
				</view>
				<view class="xilu_content_nav_box mt30">
					<view class="fs36 fw500 colf lh36">参与方式</view>
					<view class="mt45 flex-box flex-center">
						<image src="@/static/images/xilu_1.png" mode="aspectFill" class="ico40"></image>
						<image src="@/static/images/xilu_xian.png" mode="widthFix" class="ico62 mlr69"></image>
						<image src="@/static/images/xilu_2.png" mode="aspectFill" class="ico40"></image>
						<image src="@/static/images/xilu_xian.png" mode="widthFix" class="ico62 mlr69"></image>
						<image src="@/static/images/xilu_3.png" mode="aspectFill" class="ico40"></image>
					</view>
					<view class="mt20 flex-box flex-center fs28 colf lh40">
						<view>邀请好友</view>
						<view class="pl72">好友首次上课签到</view>
						<view class="pl72">获得奖励</view>
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
				width: 0,
				list: [],
				rec_count: 0,
				total_count: 0,
				auth_status: false,
				login_status: false,
				share_user_id: 0,
				web_url: ''
			}
		},
		methods: {
			//领取优惠券
			receiveCoupon(id) {
				let token = this.$api.getCache('token');
				let _this = this;
				if (!token) {
					this.auth_status = true;
					return false;
				}
				this.$http({
					url: '/addons/xilufitness/coupon/getCoupon',
					data: {
						id: id,
						is_activity: 1
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
					console.log('receiveCouponError', error);
				})
			},
			//获取优惠券数据
			getLists() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/coupon/getInviteList',
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.list = res.data.list || [];
						_this.rec_count = res.data.rec_count || 0;
						_this.total_count = res.data.total_count || 0;
						_this.width = res.data.width_per;
						_this.share_user_id = res.data.share_user_id || 0;
					}
				}).catch(error => {
					console.log('InviteCouponError', error);
				})
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
			share_friend() {
				let token = this.$api.getCache('token');
				if (!token) {
					this.auth_status = true;
				}

			}
		},
		onLoad(options) {
			this.web_url = webConfig.base_url || '';
			this.getLists();
			this.login_status = this.$api.getCache('token') || false;
			this.share_user_id = options.share_user_id || 0;
			app.globalData.share_user_id = options.share_user_id || 0;
			let token = this.$api.getCache('token');
			if (!token) {
				this.auth_status = true;
			}
		},
		onShareAppMessage() {
			let _this = this;
			return {
				title: '邀请好友',
				path: '/pages/invite/invite?share_user_id=' + _this.share_user_id
			}
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_bg {
			width: 100%;
			height: 442rpx;
			position: relative;
			z-index: 1;
		}

		&_content_nav {
			margin-top: -107rpx;
			position: relative;
			width: 100%;
			z-index: 2;

			&_box {
				width: 690rpx;
				background: #404243;
				border-radius: 20rpx;
				padding: 30rpx 37rpx 30rpx;
			}
		}

		&_coupons {
			width: 185rpx;
			height: 275rpx;
			display: inline-block;
			vertical-align: top;
			margin-right: 30rpx;
			position: relative;

			&_bg {
				width: 185rpx;
				height: 275rpx;
				position: relative;
				z-index: 1;
			}

			&_view {
				width: 185rpx;
				height: 275rpx;
				position: absolute;
				z-index: 2;
				top: 0;
				left: 0;
				right: 0;
			}

			&_btn {
				font-size: 16rpx;
				font-weight: 600;
				width: 103rpx;
				height: 33rpx;
				line-height: 33rpx;
				text-align: center;
				border-radius: 5rpx;
				background: #0E0E0F;
				margin-left: auto;
				margin-right: auto;
				margin-top: 15rpx;
				color: #F6D375;
			}

			&_btn.normal {
				font-weight: normal;
			}
		}

		&_coupons:nth-of-type(3n) {
			margin-right: 0;
		}

		&_sq {
			width: 111rpx;
			height: 2rpx;
			background: #0E0E0F;
			margin-left: auto;
			margin-right: auto;
			margin-top: 10rpx;
		}

		&_progress_nav {
			margin-top: 50rpx;
			width: 615rpx;
			height: 12rpx;
			background: #0E0E0F;
			border-radius: 8rpx;
			margin-left: auto;
			margin-right: auto;
			margin-top: 50rpx;
			position: relative;

			&_active {
				position: absolute;
				top: 0;
				left: 0;
				z-index: 2;
				height: 12rpx;
				background: #FFCF00;
				border-radius: 8rpx;
			}
		}
	}

	.pt17 {
		padding-top: 17rpx;
	}

	.mt_20 {
		margin-top: -20rpx;
	}

	.mlr69 {
		margin-left: 69rpx;
		margin-right: 69rpx;
	}

	.pl72 {
		padding-left: 72rpx;
	}
</style>
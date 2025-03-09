<template>

	<view class="xilu" v-if="login_status">
		<hx-navbar :config="config">
			<block slot="center">
				<view class="">我的</view>
			</block>
		</hx-navbar>
		<view class="page-foot">
			<Footer :identity="1" :footState="4"></Footer>
		</view>
		<view class="container">
			<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_mine_bg.png'" mode="aspectFill"
				class="xilu_mine_bg"></image>
			<view class="pr z2">
				<view class="pt20 pb40 plr40 flex-box">
					<image @click="userSetting()"
						:src="userInfo.xilufitness_urls.avatar || '../../static/images/avatar.png'" mode="aspectFill"
						class="xilu_head_img"></image>
					<view class="flex-grow-1 pl30">
						<view @click="userSetting()" class="fs40 colf fw500 lh56">{{userInfo.nickname || '未设置'}}</view>
						<view class="flex-box mt20" v-if="userInfo.is_vip == 0 && (platform != 'ios' || (platform == 'ios' && show_member == 1))"
							@click="to_member_card(rechargeInfo.id || 0)">
							<image src="@/static/images/xilu_icon13_1.png" mode="aspectFill" class="ico36"></image>
							<view class="pl10 fs28 col8 lh40">开通会员获取更多权限</view>
							<image src="@/static/images/xilu_arrow_right_yellow.png" mode="aspectFill" class="ico28">
							</image>
						</view>
					</view>
				</view>
				<view class="flex-box">
					<view class="flex-grow-1 tc" @click="to_point()">
						<view class="col2 fs42 fw500 lh60">{{userInfo.point || 0}}</view>
						<view class="mt10 fs28 colf">积分</view>
					</view>
					<view class="xilu_sq"></view>
					<view class="flex-grow-1 tc" @click="to_account()">
						<view class="col2 fs42 fw500 lh60">{{userInfo.account || 0}}</view>
						<view class="mt10 fs28 colf">余额</view>
					</view>
				</view>
				<view class="plr25 ptb40">
					<view class="xilu_vip_nav" @click="to_member_card(rechargeInfo.id || 0)" v-if="platform != 'ios' || (platform == 'ios' && show_member == 1)">
						<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_vip_card.png'" mode="aspectFill"
							class="xilu_vip_nav_bg">
						</image>
						<view class="xilu_vip_nav_view plr30 flex-box">
							<view class="flex-grow-1">
								<view class="fs42 colf lh56 fwb">会员卡</view>
								<view class="fs20 fw500 colf lh28 ls10">MEMBERSHIP CARD</view>
								<view class="mt35 fs30 colf lh42" v-if="rechargeInfo">
									开通会员卡预计省¥{{rechargeInfo.cut_amount || 0}}/年</view>
								<view class="fs26 colf lh36 tdu">查看特权</view>
							</view>
							<view class="xilu_go">{{userInfo.is_vip == 0 ? '开通' : '已开通'}}</view>
						</view>
					</view>
					<view class="xilu_box">
						<view class="fs36 fw500 colf lh36">会员服务</view>
						<view>
							<view @click="open_page('../ranking/ranking')" class="xilu_tool_item tc">
								<image src="@/static/images/xilu_tool1.png" mode="aspectFill"
									class="xilu_tool_item_cover">
								</image>
								<view>训练排名</view>
							</view>
							<view @click="open_page('../invite/invite')" class="xilu_tool_item tc">
								<image src="@/static/images/xilu_tool2.png" mode="aspectFill"
									class="xilu_tool_item_cover">
								</image>
								<view>邀请有礼</view>
							</view>
							<view @click="open_page('../my_medal/my_medal')" class="xilu_tool_item tc">
								<image src="@/static/images/xilu_tool3.png" mode="aspectFill"
									class="xilu_tool_item_cover">
								</image>
								<view>我的勋章</view>
							</view>
							<view @click="open_page('../my_coupons_list/my_coupons_list')" class="xilu_tool_item tc">
								<image src="@/static/images/xilu_tool4.png" mode="aspectFill"
									class="xilu_tool_item_cover">
								</image>
								<view>代金劵</view>
							</view>
							<view @click="open_page('../my_faviors/my_faviors')" class="xilu_tool_item tc">
								<image src="@/static/images/xilu_tool5.png" mode="aspectFill"
									class="xilu_tool_item_cover">
								</image>
								<view>我的收藏</view>
							</view>
							<view @click="open_page('../member_order/member_order')" class="xilu_tool_item tc">
								<image src="@/static/images/xilu_tool6.png" mode="aspectFill"
									class="xilu_tool_item_cover">
								</image>
								<view>会员订单</view>
							</view>
							<view @click="open_page('../about_us/about_us?is_type=2')" class="xilu_tool_item tc">
								<image src="@/static/images/xilu_tool7.png" mode="aspectFill"
									class="xilu_tool_item_cover">
								</image>
								<view>关于我们</view>
							</view>
							<view @click="open_page('../help_center/help_center?is_type=1')" class="xilu_tool_item tc">
								<image src="@/static/images/xilu_tool8.png" mode="aspectFill"
									class="xilu_tool_item_cover">
								</image>
								<view>帮助中心</view>
							</view>
						</view>
					</view>

					<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_ad.png'"
						@click="open_page('../invite/invite')" mode="aspectFill" class="xilu_ad"></image>

				</view>
			</view>
		</view>
		<u-authorize @onAuthCancel="onAuthCancel($event)" @onAuthConfirm="onAuthConfirm($event)"
			:popupStatus="auth_status" :isAuth="2"></u-authorize>
	</view>


	<view class="xilu" @tap="doLogin()" v-else>
		<hx-navbar :config="config">
			<block slot="center">
				<view class="">我的</view>
			</block>
		</hx-navbar>
		<view class="page-foot">
			<Footer :identity="1" :footState="4"></Footer>
		</view>
		<view class="container">
			<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_mine_bg.png'" mode="aspectFill"
				class="xilu_mine_bg"></image>
			<view class="pr z2">
				<view class="pt20 pb40 plr40 flex-box">
					<image src="@/static/images/avatar.png" mode="aspectFill" class="xilu_head_img"></image>
					<view class="flex-grow-1 pl30">
						<view class="fs40 colf fw500 lh56">点击登录</view>
						<view class="flex-box mt20" v-if="platform != 'ios' || (platform == 'ios' && show_member == 1)">
							<image src="@/static/images/xilu_icon13_1.png" mode="aspectFill" class="ico36"></image>
							<view class="pl10 fs28 col8 lh40">开通会员获取更多权限</view>
							<image src="@/static/images/xilu_arrow_right_yellow.png" mode="aspectFill" class="ico28">
							</image>
						</view>
					</view>
				</view>
				<view class="flex-box">
					<view class="flex-grow-1 tc">
						<view class="col2 fs42 fw500 lh60">0</view>
						<view class="mt10 fs28 colf">积分</view>
					</view>
					<view class="xilu_sq"></view>
					<view class="flex-grow-1 tc">
						<view class="col2 fs42 fw500 lh60">0</view>
						<view class="mt10 fs28 colf">余额</view>
					</view>
				</view>
				<view class="plr25 ptb40">
					<view class="xilu_vip_nav" v-if="platform != 'ios' || (platform == 'ios' && show_member == 1)">
						<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_vip_card.png'" mode="aspectFill"
							class="xilu_vip_nav_bg">
						</image>
						<view class="xilu_vip_nav_view plr30 flex-box">
							<view class="flex-grow-1">
								<view class="fs42 colf lh56 fwb">会员卡</view>
								<view class="fs20 fw500 colf lh28 ls10">MEMBERSHIP CARD</view>
								<view class="mt35 fs30 colf lh42">开通会员卡预计省¥539.2/年</view>
								<view class="fs26 colf lh36 tdu">查看特权</view>
							</view>
							<view class="xilu_go">开通</view>
						</view>
					</view>
					<view class="xilu_box">
						<view class="fs36 fw500 colf lh36">会员服务</view>
						<view>
							<view class="xilu_tool_item tc">
								<image src="@/static/images/xilu_tool1.png" mode="aspectFill"
									class="xilu_tool_item_cover">
								</image>
								<view>训练排名</view>
							</view>
							<view class="xilu_tool_item tc">
								<image src="@/static/images/xilu_tool2.png" mode="aspectFill"
									class="xilu_tool_item_cover">
								</image>
								<view>邀请有礼</view>
							</view>
							<view class="xilu_tool_item tc">
								<image src="@/static/images/xilu_tool3.png" mode="aspectFill"
									class="xilu_tool_item_cover">
								</image>
								<view>我的勋章</view>
							</view>
							<view class="xilu_tool_item tc">
								<image src="@/static/images/xilu_tool4.png" mode="aspectFill"
									class="xilu_tool_item_cover">
								</image>
								<view>代金劵</view>
							</view>
							<view class="xilu_tool_item tc">
								<image src="@/static/images/xilu_tool5.png" mode="aspectFill"
									class="xilu_tool_item_cover">
								</image>
								<view>我的收藏</view>
							</view>
							<view class="xilu_tool_item tc">
								<image src="@/static/images/xilu_tool6.png" mode="aspectFill"
									class="xilu_tool_item_cover">
								</image>
								<view>会员订单</view>
							</view>
							<view class="xilu_tool_item tc">
								<image src="@/static/images/xilu_tool7.png" mode="aspectFill"
									class="xilu_tool_item_cover">
								</image>
								<view>关于我们</view>
							</view>
							<view class="xilu_tool_item tc">
								<image src="@/static/images/xilu_tool8.png" mode="aspectFill"
									class="xilu_tool_item_cover">
								</image>
								<view>帮助中心</view>
							</view>
						</view>
					</view>

					<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_ad.png'" mode="aspectFill" class="xilu_ad">
					</image>

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
				config: {
					back: false,
					leftSlot: true,
					centerSlot: true,
					backgroundColor: [0, '#0F1011'],
					statusBarFontColor: ['#ffffff'],
					color: ['#ffffff']
				},
				web_url: '',
				login_status: false,
				auth_status: false,
				userInfo: null,
				rechargeInfo: null,
				show_member: false,
				platform: ''
			}
		},
		methods: {
			//点击登录
			doLogin() {
				this.auth_status = true;
			},
			//授权取消
			onAuthCancel(e) {
				this.login_status = false;
				this.auth_status = false;
				this.$api.toast('取消授权，可能会使部分服务不能用，或页面信息不完整')
			},
			//授权成功
			onAuthConfirm(e) {
				this.login_status = true;
				this.auth_status = false;
				this.getUserInfo();
			},
			//获取个人信息
			getUserInfo() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/user/index',
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.userInfo = res.data.info;
						_this.rechargeInfo = res.data.recharge;
						if (res.data.info.is_coach == 1) {
							_this.$api.setCache('is_coach', 1);
							_this.$api.reLaunch('../profile/profile');
						} else {
							_this.$api.delCache('is_coach');
						}
					}
				}).catch(error => {
					console.log('userError', error);
				})
			},
			//设置个人信息
			userSetting() {
				let _this = this;
				this.$api.navigate('../base_info/base_info', function(res) {
					res.eventChannel.emit('userBaseInfo', {
						userInfo: _this.userInfo
					});
					res.eventChannel.on('reloadUserInfo', function() {
						_this.getUserInfo();
					});
				})
			},
			//积分列表
			to_point() {
				let _this = this;
				this.$api.navigate('../my_point/my_point', function(res) {
					res.eventChannel.emit('userData', {
						userInfo: _this.userInfo
					});
				});
			},
			//余额列表
			to_account() {
				let _this = this;
				this.$api.navigate('../my_money/my_money', function(res) {
					res.eventChannel.emit('userData', {
						userInfo: _this.userInfo
					});
				})
			},
			//开头会员卡
			to_member_card(recharge_id) {
				let _this = this;
				this.$api.navigate('../member/member?recharge_id=' + recharge_id, function(res) {
					res.eventChannel.emit('memberCardData', {
						userInfo: _this.userInfo,
						rechargeInfo: _this.rechargeInfo
					});
				})
			},
			//打开页面
			open_page(url) {
				this.$api.navigate(url)
			},
			//判断ios端是否显示会员权益
			showIosMember() {
				this.platform = uni.getSystemInfoSync().platform;
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/home/getIosInfo',
				}).then(res => {
					_this.show_member = res.data.show_member;
				}).catch(error => {
					console.log('platformError', error);
				});

			}
		},
		onLoad() {
			this.web_url = webConfig.base_url || '';
			let token = this.$api.getCache('token');
			let is_coach = this.$api.getCache('is_coach') || 0;
			if (token) {
				this.login_status = true;
				this.getUserInfo();
				if (is_coach == 1) {
					this.$api.reLaunch('../profile/profile');
				}
			}
			this.showIosMember();
		},
		onShow() {
			if (this.login_status) {
				this.getUserInfo();
			}
		},
		onShareAppMessage() {

		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		position: relative;

		&_mine_bg {
			width: 750rpx;
			height: 630rpx;
			position: fixed;
			top: 0;
			left: 0;
			z-index: 1;
		}

		&_head_img {
			width: 130rpx;
			height: 130rpx;
			border: 5rpx solid rgba(255, 255, 255, 0.3);
			border-radius: 50%;
		}

		&_sq {
			width: 2rpx;
			height: 43rpx;
			background: #A8A8A8;
		}

		&_vip_nav {
			width: 700rpx;
			height: 251rpx;
			position: relative;

			&_bg {
				width: 700rpx;
				height: 251rpx;
				position: relative;
			}

			&_view {
				width: 700rpx;
				height: 251rpx;
				position: absolute;
				top: 0;
				left: 0;
			}
		}

		&_go {
			font-size: 30rpx;
			font-weight: 500;
			color: #FFFFFF;
			width: 106rpx;
			height: 54rpx;
			line-height: 54rpx;
			text-align: center;
			background: #D7A35F;
			border-radius: 27rpx;
		}

		&_box {
			width: 700rpx;
			padding: 30rpx 25rpx 55rpx;
			background: #2C2D2E;
			border-radius: 20rpx;
			margin-top: 20rpx;
		}

		&_tool_item {
			margin-top: 50rpx;
			font-size: 26rpx;
			font-weight: 400;
			color: #B2B2B2;
			line-height: 26rpx;
			display: inline-block;
			vertical-align: top;
			margin-right: 78rpx;
			width: 104rpx;

			&_cover {
				width: 60rpx;
				height: 61rpx;
				display: block;
				margin-left: auto;
				margin-right: auto;
				margin-bottom: 20rpx;
			}

			&:nth-of-type(4n) {
				margin-right: 0;
			}
		}

		&_ad {
			width: 700rpx;
			height: 284rpx;
			display: block;
			margin-top: 20rpx;
		}
	}

	/deep/.hx-navbar__content__main_center_txt {
		/* #ifdef MP */
		padding-left: 140rpx;
		/* #endif */
	}

	.ls10 {
		letter-spacing: 10rpx;
	}

	.tdu {
		text-decoration: underline;
	}
</style>
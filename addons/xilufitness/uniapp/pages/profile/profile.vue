<template>
	<view class="xilu">
		<hx-navbar :config="config">
			<block slot="center">
				<view class="">我的</view>
			</block>
		</hx-navbar>
		<view class="page-foot">
			<Footer :identity="2" :footState="2"></Footer>
		</view>
		<view class="container">
			<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_mine_bg.png'" mode="aspectFill" class="xilu_mine_bg"></image>
			<template v-if="login_status">

				<view class="pr z2">
					<view class="pt20 pb40 plr40 flex-box">
						<image :src="info.xilufitness_urls.coach_avatar || '../../static/images/avatar.png' "
							mode="aspectFill" class="xilu_head_img">
						</image>
						<view class="flex-grow-1 pl30">
							<view class="fs40 colf fw500 lh56">{{info.coach_name || ''}}</view>
							<view class="flex-box mt20" v-if="info.group_image">
								<image :src="info.group_image" mode="widthFix" class="ico160"></image>
							</view>
						</view>
					</view>
					<view class="flex-box">
						<view class="flex-grow-1 tc">
							<view class="col2 fs42 fw500 lh60">{{info.accountInfo.course_count || 0}}</view>
							<view class="mt10 fs28 colf">上课数量</view>
						</view>
						<view class="xilu_sq"></view>
						<view class="flex-grow-1 tc">
							<view class="col2 fs42 fw500 lh60">{{info.accountInfo.class_duration || 0}}</view>
							<view class="mt10 fs28 colf">总时长</view>
						</view>
						<view class="xilu_sq"></view>
						<view class="flex-grow-1 tc">
							<view class="col2 fs42 fw500 lh60">{{info.accountInfo.course_total_count || 0}}</view>
							<view class="mt10 fs28 colf">上课总人数</view>
						</view>
					</view>
					<view class="plr25 ptb40">
						<view class="xilu_vip_nav">
							<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_profile_card.png'" mode="aspectFill"
								class="xilu_vip_nav_bg">
							</image>
							<view class="xilu_vip_nav_view plr30 flex-box">
								<view class="flex-grow-1" @tap="redirect_url('../my_income/my_income')">
									<view class="fs28 colf lh28">可提现金额（元）</view>
									<view class="fs30 mt20 colf lh50">¥<text
											class="fs50">{{info.accountInfo.account || 0.00}}</text></view>
									<view class="fs28 colf lh28 mt40">全部金额（元）</view>
									<view class="fs30 colf lh34 mt15">¥<text
											class="fs34">{{info.accountInfo.account || 0.00}}</text></view>
								</view>
								<view @tap="redirect_url('../payouts/payouts')" class="xilu_go">提现</view>
							</view>
						</view>
						<view class="xilu_box">
							<view class="fs36 fw500 colf lh36">其他功能</view>
							<view>
								<button @tap="redirect_url('../my_grade/my_grade')" class="xilu_tool_item tc" hover-class="none">
									<image src="@/static/images/xilu_tool9.png" mode="aspectFill"
										class="xilu_tool_item_cover">
									</image>
									<view>我的等级</view>
								</button>
								<button @tap="redirect_url('../students_ranking/students_ranking')"
									class="xilu_tool_item tc" hover-class="none">
									<image src="@/static/images/xilu_tool1.png" mode="aspectFill"
										class="xilu_tool_item_cover">
									</image>
									<view>学员排名</view>
								</button>

								<!-- <button @tap="redirect_url('../my_point/my_point')" class="xilu_tool_item tc">
									<image src="@/static/images/xilu_tool10.png" mode="aspectFill"
										class="xilu_tool_item_cover">
									</image>
									<view>我的积分</view>
								</button> -->
								<button open-type="contact" class="xilu_tool_item tc" hover-class="none">
									<image src="@/static/images/xilu_tool11.png" mode="aspectFill"
										class="xilu_tool_item_cover">
									</image>
									<view>在线客服</view>
								</button>
								<button @tap="redirect_url('../report/report')" class="xilu_tool_item tc" hover-class="none">
									<image src="@/static/images/xilu_tool12.png" mode="aspectFill"
										class="xilu_tool_item_cover">
									</image>
									<view>请假报备</view>
								</button>
								<button @tap="redirect_url('../add_report/add_report')" class="xilu_tool_item tc" hover-class="none">
									<image src="@/static/images/xilu_tool13.png" mode="aspectFill"
										class="xilu_tool_item_cover">
									</image>
									<view>新增报备</view>
								</button>
								<button @tap="redirect_url('../about_us/about_us?is_type=2')" class="xilu_tool_item tc" hover-class="none">
									<image src="@/static/images/xilu_tool7.png" mode="aspectFill"
										class="xilu_tool_item_cover">
									</image>
									<view>关于我们</view>
								</button>
								<button @tap="redirect_url('../help_center/help_center?is_type=9')" class="xilu_tool_item tc" hover-class="none">
									<image src="@/static/images/xilu_tool8.png" mode="aspectFill"
										class="xilu_tool_item_cover">
									</image>
									<view>帮助中心</view>
								</button>
							</view>
						</view>
					</view>
				</view>

			</template>

			<template v-else>

				<view class="pr z2" @tap="doLogin()">
					<view class="pt20 pb40 plr40 flex-box">
						<image src="@/static/images/avatar.png" mode="aspectFill" class="xilu_head_img">
						</image>
						<view class="flex-grow-1 pl30">
							<view class="fs40 colf fw500 lh56">点击登录</view>
							<view class="flex-box mt20">
								<image src="@/static/images/xilu_label_low.png" mode="widthFix" class="ico160"></image>
							</view>
						</view>
					</view>
					<view class="flex-box">
						<view class="flex-grow-1 tc">
							<view class="col2 fs42 fw500 lh60">0</view>
							<view class="mt10 fs28 colf">上课数量</view>
						</view>
						<view class="xilu_sq"></view>
						<view class="flex-grow-1 tc">
							<view class="col2 fs42 fw500 lh60">0</view>
							<view class="mt10 fs28 colf">总时长</view>
						</view>
						<view class="xilu_sq"></view>
						<view class="flex-grow-1 tc">
							<view class="col2 fs42 fw500 lh60">0</view>
							<view class="mt10 fs28 colf">上课总人数</view>
						</view>
					</view>
					<view class="plr25 ptb40">
						<view class="xilu_vip_nav">
							<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_profile_card.png'" mode="aspectFill"
								class="xilu_vip_nav_bg">
							</image>
							<view class="xilu_vip_nav_view plr30 flex-box">
								<view class="flex-grow-1">
									<view class="fs28 colf lh28">可提现金额（元）</view>
									<view class="fs30 mt20 colf lh50">¥<text class="fs50">0</text></view>
									<view class="fs28 colf lh28 mt40">全部金额（元）</view>
									<view class="fs30 colf lh34 mt15">¥<text class="fs34">0</text></view>
								</view>
								<view class="xilu_go">提现</view>
							</view>
						</view>
						<view class="xilu_box">
							<view class="fs36 fw500 colf lh36">其他功能</view>
							<view>
								<button class="xilu_tool_item tc" hover-class="none">
									<image src="@/static/images/xilu_tool9.png" mode="aspectFill"
										class="xilu_tool_item_cover">
									</image>
									<view>我的等级</view>
								</button>
								<button class="xilu_tool_item tc" hover-class="none">
									<image src="@/static/images/xilu_tool1.png" mode="aspectFill"
										class="xilu_tool_item_cover">
									</image>
									<view>学员排名</view>
								</button>

								<button class="xilu_tool_item tc" hover-class="none">
									<image src="@/static/images/xilu_tool10.png" mode="aspectFill"
										class="xilu_tool_item_cover">
									</image>
									<view>我的积分</view>
								</button>
								<button class="xilu_tool_item tc" hover-class="none">
									<image src="@/static/images/xilu_tool11.png" mode="aspectFill"
										class="xilu_tool_item_cover">
									</image>
									<view>在线客服</view>
								</button>
								<button class="xilu_tool_item tc" hover-class="none">
									<image src="@/static/images/xilu_tool12.png" mode="aspectFill"
										class="xilu_tool_item_cover">
									</image>
									<view>请假报备</view>
								</button>
								<button class="xilu_tool_item tc" hover-class="none">
									<image src="@/static/images/xilu_tool13.png" mode="aspectFill"
										class="xilu_tool_item_cover">
									</image>
									<view>新增报备</view>
								</button>
								<button class="xilu_tool_item tc" hover-class="none">
									<image src="@/static/images/xilu_tool7.png" mode="aspectFill"
										class="xilu_tool_item_cover">
									</image>
									<view>关于我们</view>
								</button>
								<button class="xilu_tool_item tc" hover-class="none">
									<image src="@/static/images/xilu_tool8.png" mode="aspectFill"
										class="xilu_tool_item_cover">
									</image>
									<view>帮助中心</view>
								</button>
							</view>
						</view>
					</view>
				</view>
			</template>

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
				web_url:'',
				login_status: false,
				auth_status: false,
				info: null,
				userInfo: null
			}
		},
		methods: {
			//获取详情
			getInfos() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/coach/getCoachInfo',
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.info = res.data.info;
						_this.userInfo = res.data.userInfo;
					}
				}).catch(error => {
					console.log('coachInfoError', error);
				})
			},
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
				this.getInfos();
			},
			//页面跳转
			redirect_url(url) {
				let _this = this;
				this.$api.navigate(url, function(res) {
					res.eventChannel.emit('userData', {
						userInfo: _this.userInfo,
						coachInfo: _this.info
					});
					res.eventChannel.emit('coachData', {
						coachInfo: _this.info
					});
					res.eventChannel.on('coachWithdraw', function(params) {
						_this.getInfos();
					});
				});
			}
		},
		onLoad() {
			this.web_url = webConfig.base_url || '';
			let token = this.$api.getCache('token');
			let _this = this;
			let is_coach = this.$api.getCache('is_coach') || 0;
			if(!is_coach){
				this.$api.reLaunch('../mine/mine');
			}
			if (token) {
				this.login_status = true;
				this.getInfos();
			}
			uni.$on('coachCheckOrder',function(params){
				_this.getInfos();
			});
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
			height: 295rpx;
			position: relative;

			&_bg {
				width: 700rpx;
				height: 295rpx;
				position: relative;
			}

			&_view {
				width: 700rpx;
				height: 295rpx;
				position: absolute;
				top: 0;
				left: 0;
			}
		}

		&_go {
			width: 150rpx;
			height: 70rpx;
			line-height: 70rpx;
			text-align: center;
			font-size: 30rpx;
			font-weight: 400;
			color: #FFFFFF;
			background: #D7A35F;
			border-radius: 40rpx;
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

	.ico160 {
		width: 160rpx;
		height: 160rpx;
	}

	button {
		padding-left: 0;
		padding-right: 0;
		border-radius: 0;
	}
</style>
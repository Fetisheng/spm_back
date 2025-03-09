<template>
	<view class="xilu">
		<view class="page-foot">
			<Footer :identity="1" :footState="3"></Footer>
		</view>
		<view class="container plr25">
			<view class="ptb30 flex-box">
				<view class="flex-grow-1 fs30 col9">预约记录</view>
				<image src="@/static/images/xilu_icon18.png" mode="aspectFill" class="ico24"></image>
				<view @click.stop="cms_detail(4)" class="pl10 fs28 col2">退款须知</view>
			</view>
			<view class="mt30 xilu_data_link">
				<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_bg_booking.png'" mode="aspectFill" class="xilu_data_link_cover"></image>
				<view class="xilu_data_link_view flex-box plr30 flex-between">
					<view class="tc">
						<view class="fs40 colf fw500 lh56">{{userInfo.train_day || 0}}</view>
						<view class="mt10 col9 fs26 lh36">累计天数</view>
					</view>
					<view class="tc">
						<view class="fs40 colf fw500 lh56">{{userInfo.train_count || 0}}</view>
						<view class="mt10 col9 fs26 lh36">训练次数</view>
					</view>
					<view class="tc">
						<view class="fs40 colf fw500 lh56">{{userInfo.train_duration || 0}}</view>
						<view class="mt10 col9 fs26 lh36">训练时长</view>
					</view>
				</view>
			</view>

			<view class="ptb30">
				<view class="xilu_tab_item" :class="nindex==1?'active':''" @click="chooseNav(1)">团课</view>
				<view class="xilu_tab_item" :class="nindex==2?'active':''" @click="chooseNav(2)">私教</view>
				<view class="xilu_tab_item" :class="nindex==3?'active':''" @click="chooseNav(3)">活动</view>
			</view>
			<template v-if="nindex==1">
				<template v-if="list.length > 0">
					<view class="xilu_tk_item" v-for="(vo,index) in list">
						<view class="flex-box" @click="detail(vo.id)">
							<image :src="vo.goods.course_camp.coach.xilufitness_urls.coach_avatar || vo.goods.xilufitness_urls.thumb_image" mode="aspectFill"
								class="xilu_tk_item_cover">
							</image>
							<view class="flex-grow-1 pl20">
								<view class="fs32 fw500 colf lh46 m-ellipsis">{{vo.goods.goods_name || ''}}</view>
								<view class="mt15 lh32 fs24 col2">{{vo.goods.course_camp.class_time_txt || ''}}
									{{vo.goods.course_camp.start_at || ''}}～{{vo.goods.course_camp.end_at || ''}}
								</view>
								<view class="flex-box mt15" @tap.stop="openLocation(vo.shop.lat,vo.shop.lng)">
									<image src="@/static/images/xilu_icon_address_gray.png" mode="aspectFill"
										class="ico28">
									</image>
									<view class="m-ellipsis flex-grow-1 pl10 pr20 fs28 col9 lh40">
										{{vo.shop.address || ''}}
									</view>
									<image src="@/static/images/xilu_guide.png" mode="aspectFill" class="ico30"></image>
								</view>
							</view>
						</view>
						<view class="flex-box flex-end mt30">
							<view v-if="vo.show_cancel == 1 && vo.order_status == 1" @click="cancelOrder(vo.id)"
								class="xilu_btn1">取消预约</view>
							<view @click="detail(vo.id)" class="xilu_btn1">查看详情</view>
							<view v-if="vo.order_status == 1 || vo.order_status == 2" @click="codeOpen(vo)"
								class="xilu_btn2">查看签到码</view>
							<view v-if="vo.order_status == 3"  @click="to_comment(vo.id)" class="xilu_btn1">去评价</view>
							<view class="xilu_btn3" v-if="vo.order_status == 6">课时已完成</view>
							<view class="xilu_btn3" v-if="vo.order_status == 5">已过期</view>
						</view>
					</view>
				</template>

				<template v-else>
					<empty-data :tips="'暂无预约记录'" :lineHeight="100"></empty-data>
				</template>

			</template>

			<template v-if="nindex==2">

				<template v-if="list.length > 0">

					<view class="xilu_tk_item" v-for="(vo,index) in list">
						<view class="flex-box" @click="detail(vo.id)">
							<image :src="vo.goods.course_camp.coach.xilufitness_urls.coach_avatar || vo.goods.xilufitness_urls.thumb_image" mode="aspectFill"
								class="xilu_tk_item_cover">
							</image>
							<view class="flex-grow-1 pl20">
								<view class="fs32 fw500 colf lh46 m-ellipsis">{{vo.goods.goods_name || ''}}</view>
								<view class="mt15 fs24 col9 lh32">已签到<text
										class="col2">{{vo.code_num || 0}}</text>/{{vo.code_total_num || 0}}</view>
								<view class="flex-box mt15" @tap.stop="openLocation(vo.shop.lat,vo.shop.lng)">
									<image src="@/static/images/xilu_icon_address_gray.png" mode="aspectFill"
										class="ico28">
									</image>
									<view class="m-ellipsis flex-grow-1 pl10 pr20 fs28 col9 lh40">
										{{vo.shop.address || ''}}
									</view>
									<image src="@/static/images/xilu_guide.png" mode="aspectFill" class="ico30"></image>
								</view>
							</view>
						</view>

						<view class="flex-box flex-end mt20">
							<view @click="detail(vo.id)" class="xilu_btn1">查看详情</view>
							<view v-if="vo.order_status == 1 || vo.order_status == 2" @click="codeOpen(vo)"
								class="xilu_btn2">查看签到码</view>
								<view v-if="vo.order_status == 3" @click="to_comment(vo.id)" class="xilu_btn1">去评价</view>
							<view class="xilu_btn3" v-if="vo.order_status == 6">课时已完成</view>
							<view class="xilu_btn3" v-if="vo.order_status == 5">已过期</view>
						</view>
					</view>

				</template>

				<template v-else>
					<empty-data :tips="'暂无预约记录'" :lineHeight="100"></empty-data>
				</template>

			</template>

			<template v-if="nindex==3">
				<template v-if="list.length > 0">
					<view class="xilu_tk_item" v-for="(vo,index) in list">
						<view class="flex-box" @click="detail(vo.id)">
							<image :src="vo.goods.course_camp.coach.xilufitness_urls.coach_avatar || vo.goods.xilufitness_urls.thumb_image" mode="aspectFill"
								class="xilu_tk_item_cover">
							</image>
							<view class="flex-grow-1 pl20">
								<view class="fs32 fw500 colf lh46 m-ellipsis">{{vo.goods.goods_name || ''}}</view>
								<view class="mt15 lh32 fs24 col2">
									{{vo.goods.course_camp.start_at || ''}}～{{vo.goods.course_camp.end_at || ''}}</view>
								<view class="mt15 fs24 col9 lh32">已签到<text
										class="col2">{{vo.code_num || 0}}</text>/{{vo.code_total_num || 0}}</view>
							</view>
						</view>
						<view class="flex-box mt15" @tap.stop="openLocation(vo.shop.lat,vo.shop.lng)">
							<image src="@/static/images/xilu_icon_address_gray.png" mode="aspectFill" class="ico28">
							</image>
							<view class="m-ellipsis flex-grow-1 pl10 pr20 fs28 col9 lh40">{{vo.shop.address || ''}}
							</view>
							<image src="@/static/images/xilu_guide.png" mode="aspectFill" class="ico30"></image>
						</view>
						<view class="flex-box flex-end mt20">
							<view v-if="vo.show_cancel == 1  && vo.order_status == 1" @click="cancelOrder(vo.id)"
								class="xilu_btn1">取消预约</view>
							<view @click="detail(vo.id)" class="xilu_btn1">查看详情</view>
							<view v-if="vo.order_status == 1 || vo.order_status == 2" @click="codeOpen(vo)"
								class="xilu_btn2">查看签到码</view>
								<view v-if="vo.order_status == 3" @click="to_comment(vo.id)" class="xilu_btn1">去评价</view>
							<view class="xilu_btn3" v-if="vo.order_status == 6">课时已完成</view>
							<view class="xilu_btn3" v-if="vo.order_status == 5">已过期</view>
						</view>
					</view>
				</template>
				<template v-else>
					<empty-data :tips="'暂无预约记录'" :lineHeight="100"></empty-data>
				</template>

			</template>

		</view>

		<u-popup :show="showCode" mode="center" bgColor="transparent" @open="codeOpen" @close="codeClose"
			:safeAreaInsetBottom="false">
			<view class="xilu_code_popup pr pt40">
				<view class="tc fs34 col9 lh40">已签到<text
						class="col2">{{code_info.code_num || 0}}</text>/{{code_info.code_total_num || 1}}</view>
				<image @click="codeClose()" src="@/static/images/xilu_close.png" mode="aspectFill" class="xilu_close">
				</image>

				<image :src="code_info.code_url" mode="aspectFill" class="xilu_code"></image>
				<view class="tc fs34 colf lh40">出示二维码核销</view>
			</view>
		</u-popup>
	</view>
</template>

<script>
	const app = getApp();
	const webConfig = require("@/util/config");
	export default {
		data() {
			return {
				nindex: 1,
				auth_status: false,
				code_info: null,
				showCode: false,
				userInfo: null,
				list: [],
				page: 1,
				total_count: 0,
				web_url:'',
			}
		},
		methods: {
			chooseNav(e) {
				this.nindex = e
				this.clearData();
			},
			//显示核销二维码
			codeOpen(info) {
				this.code_info = info;
				this.showCode = true
			},
			codeClose() {
				this.showCode = false
			},
			//打开地图
			openLocation(lat, lng) {
				this.$api.openLocation(lat, lng);
			},
			//授权取消
			onAuthCancel(e) {
				this.auth_status = false;
				this.$api.toast('取消授权，可能会使部分服务不能用，或页面信息不完整')
			},
			//授权成功
			onAuthConfirm(e) {
				this.auth_status = false;
				this.getUserInfo();
				this.getLists();
			},
			//清空数据
			clearData() {
				this.page = 1;
				this.list = [];
				this.total_count = 0;
				this.getLists();
			},
			//订单列表
			getLists() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/order/getOrderList',
					data: {
						page: _this.page,
						order_type: _this.nindex,
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1 && res.data.list.length > 0) {
						if (_this.page > 1) {
							_this.list.push(...res.data.list)
						} else {
							_this.list = res.data.list || [];
						}
						_this.total_count = res.data.total_count;
					}
				}).catch(error => {
					console.log('orderListError', error);
				})
			},
			//获取用户详情
			getUserInfo() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/user/index',
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.userInfo = res.data.info;
					}
				}).catch(error => {
					console.log('userInfoError', error);
				})
			},
			// 订单详情
			detail(id) {
				this.$api.navigate('../booking_info/booking_info?id=' + id);
			},
			//取消预约
			cancelOrder(id) {
				let _this = this;
				this.$api.modal('温馨提示', '确定取消么?', function(res) {
					if (res.confirm) {
						_this.$http({
							url: '/addons/xilufitness/order/cancelOrder',
							data: {
								id: id
							},
							method: 'post'
						}).then(res => {
							if (res.code == 1) {
								_this.clearData();
							} else {
								_this.$api.toast(res.msg || '取消失败');
							}
						}).catch(error => {
							console.log('orderCancelError', error);
						});
					}
				})
			},
			//退款须知
			cms_detail(is_type) {
				this.$api.navigate('../about_us/about_us?is_type=' + is_type);
			},
			//去评价
			to_comment(id) {
				let _this = this;
				this.$api.navigate('../white_appraise/white_appraise?id=' + id,function(res){
					res.eventChannel.on('reloadOrder',function(){
						_this.clearData();
					});
				});
			}
		},
		onLoad() {
			this.web_url = webConfig.base_url || '';
			let token = this.$api.getCache('token');
			if (!token) {
				this.auth_status = true;
			} else {
				this.getUserInfo();
				this.getLists();
			}
		},
		onReachBottom() {
			let _this = this;
			if (this.total_count > this.list.length) {
				this.page = this.page + 1
				this.getLists();
			}
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_data_link {
			width: 700rpx;
			height: 207rpx;
			position: relative;

			&_cover {
				width: 700rpx;
				height: 207rpx;
				position: relative;
				z-index: 1;
			}

			&_view {
				width: 700rpx;
				height: 207rpx;
				position: absolute;
				z-index: 2;
				top: 0;
				left: 0;
				right: 0;
			}
		}

		&_tab_item {
			margin-right: 40rpx;
			font-size: 34rpx;
			font-weight: 500;
			color: #999999;
			line-height: 48rpx;
			display: inline-block;
			vertical-align: top;

			&.active {
				font-size: 34rpx;
				font-weight: 500;
				color: #FFFFFF;
			}
		}

		&_tk_item {
			width: 700rpx;
			margin-bottom: 30rpx;
			background: #292B2C;
			border-radius: 20rpx;
			padding: 20rpx 20rpx 30rpx;

			&_cover {
				width: 150rpx;
				height: 150rpx;
				border-radius: 20rpx;
			}
		}

		&_btn1 {
			width: 206rpx;
			height: 70rpx;
			line-height: calc(70rpx - 2px);
			text-align: center;
			font-size: 28rpx;
			font-weight: 400;
			color: #FFFFFF;
			border-radius: 20rpx;
			border: 1px solid #999999;

		}

		&_btn1+&_btn1 {
			margin-left: 20rpx;
		}

		&_btn2 {
			width: 206rpx;
			height: 70rpx;
			line-height: 70rpx;
			text-align: center;
			font-size: 28rpx;
			font-weight: 400;
			color: #0E0E0F;
			background: #FFCF00;
			border-radius: 20rpx;
			margin-left: 20rpx;
		}

		&_btn1+&_btn3 {
			margin-left: 20rpx;
		}

		&_btn3 {
			width: 206rpx;
			height: 70rpx;
			line-height: 70rpx;
			text-align: center;
			font-size: 28rpx;
			font-weight: 400;
			color: #0E0E0F;
			background: #999999;
			border-radius: 20rpx;
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
	}
</style>
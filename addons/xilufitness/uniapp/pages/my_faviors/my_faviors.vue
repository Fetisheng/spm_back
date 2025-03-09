<template>
	<view class="xilu">
		<view class="page-head bg-normal">
			<scroll-view scroll-x="true" class="xilu_top_tab">
				<view class="xilu_top_tab_item" :class="nindex==1?'active':''" @click="chooseNav(1)">门店</view>
				<view class="xilu_top_tab_item" :class="nindex==2?'active':''" @click="chooseNav(2)">教练</view>
			</scroll-view>
		</view>
		<view class="container" style="padding-top: 108rpx;">
			<view class="ptb25 plr25" v-if="nindex==1">
				<template v-if="list.length > 0">
					<view class="xilu_item_part1" v-for="(vo,index) in list" @tap="shop_info(vo.shop.id)">
						<image :src="vo.shop.xilufitness_urls.shop_image" mode="aspectFill"
							class="xilu_item_part1_cover">
						</image>
						<view class="pl20 ptb20 pr30">
							<view class="m-ellipsis fs30 fw500 colf lh42">{{vo.shop.shop_name || ''}}</view>
							<view class="flex-box flex-between mt10">
								<view class="m-ellipsis fs28 col9 w512">{{vo.shop.address || ''}}</view>
								<!-- 	<view class="col2 fs24"> <distance-format :distance="vo.shop.distance"></distance-format>
								</view> -->
							</view>
						</view>
					</view>
				</template>

				<template v-else>
					<empty-data :tips="'暂无收藏'" :lineHeight="300"></empty-data>
				</template>

			</view>
			<view class="pb25 plr25" v-if="nindex==2">
				<template v-if="list.length > 0">
					<template v-for="(vo,index) in list">
						<view class="flex-box ptb30" @tap="shop_info(vo.id)">
							<view class="flex-grow-1 fs30 cola">{{vo.shop_name || ''}} <distance-format
									:distance="vo.distance"></distance-format>
							</view>
							<view class="fs30 cola">门店详情</view>
							<image src="@/static/images/xilu_arrow_right.png" mode="widthFix" class="ico12 ml10">
							</image>
						</view>
						<view class="xilu_box1">
							<template v-if="vo.coach && vo.coach.list.length > 0">

								<view class="xilu_box1_item flex-box" v-for="(vv,keys) in vo.coach.list"
									@tap="coach_info(vv.id,vo.id)">
									<image :src="vv.xilufitness_urls.coach_avatar" mode="aspectFill"
										class="xilu_box1_cover"></image>
									<view class="flex-grow-1 plr20">
										<view class="flex-box">
											<view class="fs30 fw500 colf lh42">{{vv.coach_name || ''}}</view>
											<image :src="vv.coach_group_image" mode="widthFix" class="xilu_label ml20">
											</image>
										</view>
										<view class="mt25 fs30 colf lh42" v-if="vv.lable_list.length > 0">
											{{vv.lable_list.join('｜')}}
										</view>
									</view>
									<view class="xilu_btn">预约</view>
								</view>

							</template>

							<template v-else>
								<empty-data :tips="'暂无教练'" :lineHeight="100"></empty-data>
							</template>

						</view>


					</template>
				</template>

				<template v-else>
					<empty-data :tips="'暂无收藏'" :lineHeight="300"></empty-data>
				</template>

			</view>
		</view>
	</view>
</template>

<script>
	const app = getApp();
	export default {
		data() {
			return {
				tabList: ['门店', '教练'],
				nindex: 1,
				list: []
			}
		},
		methods: {
			chooseNav(e) {
				this.nindex = e
				this.getLists();
			},
			//数据列表
			getLists() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/user/myCollect',
					data: {
						is_type: _this.nindex,
						lat: app.globalData.lat || '',
						lng: app.globalData.lng || ''
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.list = res.data.list;
					}
				}).catch(error => {
					console.log('collectError', error);
				})
			},
			//门店详情
			shop_info(id) {
				this.$api.navigate('../stores_info/stores_info?id=' + id);
			},
			//教练详情
			coach_info(id, shop_id) {
				this.$api.navigate('../trainer_info/trainer_info?id=' + id + '&shop_id=' + shop_id);
			}
		},
		onLoad() {
			this.getLists();
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
			padding-left: 30rpx;
			padding-right: 30rpx;

			&_item {
				width: 120rpx;
				margin-right: 30rpx;
				font-size: 34rpx;
				font-family: PingFangSC, PingFang SC;
				font-weight: 400;
				color: #999999;
				position: relative;
				height: 108rpx;
				line-height: 108rpx;
				display: inline-block;
				vertical-align: top;
				text-align: center;
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

		&_item_part1 {
			width: 700rpx;
			background: #292B2C;
			border-radius: 20rpx;
			margin-bottom: 20rpx;

			&_cover {
				width: 100%;
				height: 325rpx;
				display: block;
				border-radius: 20rpx 20rpx 0 0;
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

		&_label {
			width: 161rpx;
			height: 38rpx;
		}
	}
</style>
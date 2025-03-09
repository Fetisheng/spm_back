<template>
	<view class="xilu">
		
		<view class="page-foot bg-58" v-if="userInfo">
			<view class="xilu_foot_nav flex-box">
				<view class="fs28 fw500 colf">{{userInfo.rownum || 0}}</view>
				<image :src="userInfo.avatar || '../../static/images/avatar.png'" mode="aspectFill" class="ico120 ml20">
				</image>
				<view class="flex-grow-1 pl30">
					<view class="m-ellipsis fs34 fw500 colf lh36">{{userInfo.nickname || ''}}</view>
					<view class="col2 fs30 lh42 mt30">{{userInfo.train_duration || 0}}小时</view>
				</view>
			</view>
		</view>
		
		<view class="container">
			<view class="plr30 pt30 pb120">
				<view class="xilu_box">

					<template v-if="list.length > 0">
						<view class="xilu_item flex-box" v-for="(vo,keys) in list">
							
							<image v-if="keys <= 2" :src="'../../static/images/xilu_top'+(keys + 1)+'.png'" mode="aspectFill" class="xilu_item_left"></image>
							<view v-else class="xilu_item_left">{{keys + 1}}</view>
							<image :src="vo.xilufitness_urls.avatar || '../../static/images/avatar.png'" mode="aspectFill" class="ico120 ml30">
							</image>
							<view class="flex-grow-1 pl30">
								<view class="m-ellipsis fs34 fw500 colf lh36">{{vo.nickname || ''}}</view>
								<view class="col2 fs30 lh42 mt30">{{vo.train_duration || 0}}小时</view>
							</view>
						</view>
					</template>

					<template v-else>
						<empty-data :tips="'暂无数据'" :lineHeight="300"></empty-data>
					</template>


				</view>
			</view>
		</view>
	</view>
</template>

<script>
	const app = getApp();
	export default {
		data() {
			return {
				page: 1,
				list: [],
				total_count: 0,
				userInfo: null,
			}
		},
		methods: {
			//获取排名列表
			getLists() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/user/getMyRanking',
					data: {
						page: _this.page
					},
					method: 'GET'
				}).then(res => {
					if (res.code == 1) {
						if (_this.page > 1) {
							_this.list.push(...res.data.list);
						} else {
							_this.list = res.data.list;
						}
						_this.total_count = res.data.total_count;
						_this.userInfo = res.data.userInfo;
					}
				}).catch(error => {
					console.log('rankingError', error);
				})
			}
		},
		onLoad() {
			this.getLists();
		},
		onReachBottom() {
			if (this.total_count > this.list.length) {
				this.page = this.page + 1;
				this.getLists();
			}
		},
		onShareAppMessage() {
		
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_box {
			background: #292B2C;
			border-radius: 20rpx;
			padding: 30rpx;
		}

		&_item {
			width: 630rpx;
			height: 200rpx;
			background: #404243;
			border-radius: 20rpx;
			padding-left: 30rpx;
			padding-right: 30rpx;

			&_left {
				width: 40rpx;
				height: 40rpx;
				line-height: 40rpx;
				text-align: center;
				font-size: 28rpx;
				font-weight: 500;
				color: #FFFFFF;
			}
		}

		&_item+&_item {
			margin-top: 30rpx;
		}

		&_foot_nav {
			width: 750rpx;
			height: 200rpx;
			background: #585A5C;
			padding-left: 90rpx;
			padding-right: 90rpx;
		}
	}

	.ico120 {
		width: 120rpx;
		height: 120rpx;
		border-radius: 50%;

	}

	.pb120 {
		padding-bottom: 120rpx;
	}
</style>
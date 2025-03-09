<template>
	<view class="xilu">
		<view class="container">
			<view class="plr30 ptb30">
				<view class="xilu_box">
					<template v-if="list.length > 0">
						<view class="xilu_item flex-box" v-for="(vo,index) in list">
							<image v-if="index <= 2" :src="'../../static/images/xilu_top'+(index + 1)+'.png'" mode="aspectFill" class="xilu_item_left"></image>
							<view  v-else class="xilu_item_left">{{index + 1}}</view>
							<image :src="vo.user.xilufitness_urls.avatar || '../../static/images/avatar.png'" mode="aspectFill" class="ico120 ml30">
							</image>
							<view class="flex-grow-1 pl30">
								<view class="m-ellipsis fs34 fw500 colf lh36">{{vo.user.nickname || vo.user.mobile}}</view>
								<view class="col2 fs30 lh42 mt30">{{vo.user.train_duration || 0}}小时</view>
							</view>
						</view>
					</template>
					<template v-else>
						<empty-data :tips="'暂无学员'" :lineHeight="100"></empty-data>
					</template>

				</view>
			</view>
		</view>
	</view>
</template>

<script>
	
	var eventChannel = null;
	export default {
		data() {
			return {
				coachInfo: null,
				list: [],
				page: 1,
				total_count: 0
			}
		},
		methods: {
			//获取学员排名
			getStudentList(coach_id) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/coach/getStudentRanking',
					data: {
						page: _this.page,
						coach_id: coach_id
					},
					method: 'GET'
				}).then(res => {
					if (res.code == 1) {
						if (_this.page > 1) {
							_this.list.push(res.data.list);
						} else {
							_this.list = res.data.list;
						}
						_this.total_count = res.data.total_count;
					}
				}).catch(error => {
					console.log('studentRankingError', error);
				})
			}

		},
		onLoad() {
			let _this = this;
			eventChannel = this.getOpenerEventChannel();
			eventChannel.on("coachData", function(params) {
				_this.coachInfo = params.coachInfo || '';
				_this.getStudentList(_this.coachInfo.id || 0);
			});
		},
		onReachBottom() {
			if (this.total_count > this.list.length) {
				this.page = this.page + 1;
				this.getStudentList(this.coachInfo.id || 0);
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
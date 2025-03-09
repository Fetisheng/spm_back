<template>
	<view class="xilu">
		<view class="page-foot bg-normal" >
			<view class="pb30 plr25">
				<view class="btn1" @click="addReport()">新增报备</view>
			</view>
		</view>
		<view class="container">
			<view class="ptb15 plr25">
				<template v-if="list.length > 0">
					<view class="xilu_item" v-for="(vo,index) in list">
						<view class="fs32 col9 lh46">请假时间：</view>
						<view class="mt10 fs32 colf lh46">{{vo.start_at || ''}} ～ {{vo.end_at || ''}}</view>
						<view class="mt20 fs32 col9 lh46">请假事由：</view>
						<view class="mt10 fs32 colf lh46">{{vo.description || ''}}</view>
					</view>
				</template>
				<template v-else>
					<empty-data :tips="'暂无报备记录'" :lineHeight="300"></empty-data>
				</template>

			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				list: [],
				page: 1,
				total_count: 0,
				coachInfo: null
			}
		},
		methods: {
			//获取请假数据
			getLists() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/coach/getReports',
					data: {
						page: _this.page,
						id: _this.coachInfo.id || 0
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						if (_this.page > 1) {
							_this.list.push(...res.data.list);
						} else {
							_this.list = res.data.list;
						}
						_this.total_count = res.data.total_count;
					}
				}).catch(error => {
					console.log('reportLitsError', error);
				})
			},
			//新增报备
			addReport() {
				let _this = this;
				this.$api.navigate('../add_report/add_report', function(res) {
					res.eventChannel.on('reloadReport', function() {
						_this.clearData();
					});
				})
			},
			//清除数据
			clearData() {
				this.page = 1;
				this.total_count = 1;
				this.list = [];
			}
		},
		onLoad() {
			let eventChannel = this.getOpenerEventChannel();
			let _this = this;
			eventChannel.on('userData', function(params) {
				console.log('params',params);
				_this.coachInfo = params.coachInfo || null;
				_this.getLists();
			});
		},
		onReachBottom() {
			if (this.total_count > this.list.length) {
				this.page = this.page + 1;
				this.getLists();
			}
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_item {
			width: 700rpx;
			margin-bottom: 30rpx;
			background: #292B2C;
			border-radius: 20rpx;
			padding: 30rpx 30rpx 20rpx;
		}
	}
</style>
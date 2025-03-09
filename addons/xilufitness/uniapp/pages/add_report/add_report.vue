<template>
	<view class="xilu">
		<view class="page-foot bg-normal">
			<view class="pb30 plr25">
				<view class="btn1" @click="addReport">提交</view>
			</view>
		</view>
		<view class="container">
			<view class="pt30 plr25">
				<view class="pl5 fs36 colf lh36">选择时间</view>
				<view class="flex-box mt30">
					<view class="xilu_time_click" @tap="chooseDate('start_at')" :class="start_at?'ac':''">
						{{start_at || '开始时间'}}
					</view>
					<view class="xilu_sq"></view>
					<view class="xilu_time_click" @tap="chooseDate('end_at')" :class="end_at?'ac':''">
						{{end_at || '结束时间'}}
					</view>
				</view>
				<view class="mt70 colf pl5 fs36 lh36">请假事由</view>
				<textarea class="xilu_tex" v-model="description" placeholder="请输入" placeholder-class="col9" cols="30"
					rows="10"></textarea>
			</view>
		</view>
		<u-popup :show="showtime" mode="bottom" @close="closeTime" @open="openTime" bgColor="transparent">
			<view class="xilu_popup">
				<view class="flex-box flex-between xilu_popup_top">
					<view class="fs34 fw500 colf lh44">选择时间</view>
					<image src="@/static/images/xilu_close.png" mode="aspectFill" class="ico32"></image>
				</view>
				<picker-view :indicator-style="indicatorStyle" :value="value" @change="bindChange"
					class="xilu_picker_view" :mask-style="maskStyle">

					<picker-view-column>
						<view class="xilu_picker_view_item" :class="index1==index?'picker_item':''"
							v-for="(item,index) in months" :key="index">{{item}}月</view>
					</picker-view-column>
					<picker-view-column>
						<view class="xilu_picker_view_item" :class="index2==index?'picker_item':''"
							v-for="(item,index) in days" :key="index">{{item}}日</view>
					</picker-view-column>
					<picker-view-column>
						<view class="xilu_picker_view_item" :class="index3==index?'picker_item':''"
							v-for="(item,index) in hours" :key="index">{{item}}</view>
					</picker-view-column>
				</picker-view>
				<view class="plr25">
					<view @tap="closeTime()" class="btn1">确认</view>
				</view>
			</view>
		</u-popup>
	</view>
</template>

<script>
	const app = getApp();
	var eventChannel = null;
	export default {
		data() {
			const date = new Date()
			const months = []
			const year = date.getFullYear()
			const month = date.getMonth() + 1
			const days = []
			const day = date.getDate()
			for (let i = 1; i <= 12; i++) {
				months.push(i)
			}
			for (let i = 1; i <= 31; i++) {
				days.push(i)
			}

			return {
				showtime: false,
				hours: ['00:00', '01:00', '02:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00', '09:00',
					'10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00',
					'21:00', '22:00', '23:00'
				],
				hour: '08:00',
				title: 'picker-view',
				year,
				months,
				month,
				days,
				day,
				value: [month - 1, day - 1, 0],
				visible_start: false,
				visible_end: false,
				indicatorStyle: `height: 39px;background: #292B2C;z-index:0;`,
				maskStyle: `background: #404243;z-index:0;`,
				index1: month - 1,
				index2: day - 1,
				index3: 0,
				start_at: '',
				end_at: '',
				description: '',
				coachInfo: null
			}
		},
		onShow() {
			console.log('1111');
			let da = new Date();
			let hour = da.getHours()
			this.value[2] = hour + 1;
			console.log(this.value);
		},
		methods: {
			closeTime() {
				this.showtime = false
				this.visible_start = false;
				this.visible_end = false;
			},
			openTime() {
				this.showtime = true
			},
			bindChange: function(e) {
				const val = e.detail.value
				this.index1 = val[0]
				this.index2 = val[1]
				this.index3 = val[2]
				this.month = this.months[val[0]]
				this.day = this.days[val[1]]
				this.hour = this.hours[val[2]]
				if (this.visible_start) {
					this.start_at = this.year + '-' + this.month + '-' + this.day + ' ' + this.hour;
				}
				if (this.visible_end) {
					this.end_at = this.year + '-' + this.month + '-' + this.day + ' ' + this.hour;
				}
			},
			//选择开始时间
			chooseDate(is_type) {
				if (is_type == 'start_at') {
					this.showtime = true;
					this.visible_start = true;
					this.visible_end = false;
				} else {
					this.showtime = true;
					this.visible_start = false;
					this.visible_end = true;
				}
			},
			//提交请假
			addReport() {
				let _this = this;
				if (!this.start_at) {
					this.$api.toast('请选择开始时间');
				} else if (!this.end_at) {
					this.$api.toast('请选择结束时间')
				} else if (!this.description) {
					this.$api.toast('请填写请假事由');
				} else if (!_this.coachInfo) {
					this.$api.toast('教练不存在');
				} else {
					this.$http({
						url: '/addons/xilufitness/coach/addReport',
						data: {
							start_at: _this.start_at,
							end_at: _this.end_at,
							description: _this.description,
							id: _this.coachInfo.id || 0
						},
						method: 'post'
					}).then(res => {
						if (res.code == 1) {
							this.$api.toast('提交成功');
							eventChannel.emit('reloadReport', {});
							this.$api.back(2000);
						} else {
							this.$api.modal('温馨提示', res.msg, function() {}, false);
						}
					}).catch(error => {
						console.log('addReportError', error);
					});
				}
			},
			//获取详情
			getInfos() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/coach/getCoachInfo',
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.coachInfo = res.data.info;
					}
				}).catch(error => {
					console.log('coachInfoError', error);
				})
			}
		},
		onLoad() {
			this.getInfos();
			// let _this = this;
			// eventChannel = this.getOpenerEventChannel();
			// eventChannel.on('userData', function(params) {
			// 	_this.coachInfo = params.coachInfo || null;
			// });
		},
		onShareAppMessage() {

		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_time_click {
			width: 300rpx;
			height: 80rpx;
			line-height: 80rpx;
			text-align: center;
			font-size: 30rpx;
			font-weight: 400;
			color: #999999;
			background: #292B2C;
			border-radius: 15rpx;

			&.ac {
				color: #fff;
			}
		}

		&_sq {
			width: 29rpx;
			height: 1px;
			background: #EEEEEE;
			border-radius: 45rpx;
			margin-left: 35rpx;
			margin-right: 35rpx;
		}

		&_tex {
			width: 700rpx;
			height: 288rpx;
			background: #292B2C;
			border-radius: 20rpx;
			margin-top: 30rpx;
			padding: 30rpx;
			font-size: 30rpx;
			font-weight: 400;
			color: #fff;
		}

		&_popup {
			width: 750rpx;
			background: #404243;
			border-radius: 20rpx 20rpx 0rpx 0rpx;
			position: relative;
			padding-bottom: 30rpx;

			&_top {
				height: 104rpx;
				width: 100%;
				line-height: 104rpx;
				padding-left: 40rpx;
				padding-right: 40rpx;
				border-bottom: 1px solid #999999;
			}
		}

		&_picker_view {
			width: 750rpx;
			height: 464rpx;
			margin-top: 50rpx;
			margin-bottom: 45rpx;

			&_item {
				font-size: 36rpx;
				line-height: 39px;
				color: #999999;
				text-align: center;
			}

			&_item.picker_item {
				font-size: 36rpx;
				line-height: 39px;
				color: #fff;
			}
		}
	}
</style>
<template>
	<view class="xilu">
		<view class="page-foot">
			<view class="xilu_foot_nav" @click.stop="comment_post()">
				<view class="btn1">提交评价</view>
			</view>
		</view>
		<view class="container">
			<view class="ptb30 plr25">

				<!-- <view class="xilu_info" v-if="type==0">
					<view class="flex-box">
						<image src="@/static/logo.png" mode="aspectFill" class="xilu_info_cover"></image>
						<view class="flex-grow-1 pl20">
							<view class="fs32 fw500 colf lh46">力量训练</view>
							<view class="fs24 col2 lh32 mt15">11月16日 14:20～15:20</view>
							<view class="flex-box mt20">
								<image src="@/static/images/xilu_icon24.png" mode="widthFix" class="ico28"></image>
								<view class="pl10 flex-grow-1 m-ellipsis fs28 col9 lh40">上海市徐汇区石龙路壹方天地上海市徐汇区石龙路壹方天地
								</view>
							</view>
						</view>
					</view>
				</view> -->

				<template v-if="info">

					<view class="xilu_info" v-if="info.order_type != 3">
						<view class="flex-box">
							<image :src="info.course_camp.course.xilufitness_urls.thumb_image" mode="aspectFill"
								class="xilu_info_cover"></image>
							<view class="flex-grow-1 pl20">
								<view class="fs32 fw500 colf lh46">{{info.course_camp.course.title || ''}}</view>
								<view class="mt15 fs24 col9 lh34">已签到<text
										class="col2">{{info.code_num || 0 }}</text>/{{info.code_total_num || 0}}</view>
								<view class="flex-box mt15"
									@click.stop="openLocation(info.course_camp.shop.lat,info.course_camp.shop.lng)">
									<image src="@/static/images/xilu_icon24.png" mode="widthFix" class="ico28"></image>
									<view class="pl10 flex-grow-1 m-ellipsis fs28 col9 lh40 pr20">
										{{info.course_camp.shop.address || ''}}
									</view>
									<image src="@/static/images/xilu_guide.png" mode="widthFix" class="ico30"></image>
								</view>
							</view>
						</view>
					</view>

					<view class="xilu_info" v-else>
						<view class="flex-box">
							<image :src="info.course_camp.course.xilufitness_urls.thumb_image" mode="aspectFill"
								class="xilu_info_cover"></image>
							<view class="flex-grow-1 pl20">
								<view class="fs32 fw500 colf lh46">{{info.course_camp.course.title || ''}}</view>
								<view class="fs24 col2 lh32 mt15">11月23日-12月17日</view>
								<view class="mt15 fs24 col9 lh34">已签到<text
										class="col2">{{info.code_num || 0 }}</text>/{{info.code_total_num || 0}}</view>
							</view>
						</view>
						<view class="flex-box mt15"
							@click.stop="openLocation(info.course_camp.shop.lat,info.course_camp.shop.lng)">
							<image src="@/static/images/xilu_icon24.png" mode="widthFix" class="ico28"></image>
							<view class="pl10 flex-grow-1 m-ellipsis fs28 col9 lh40 pr20">
								{{info.course_camp.shop.address || ''}}
							</view>
							<image src="@/static/images/xilu_guide.png" mode="widthFix" class="ico30"></image>
						</view>
					</view>
				</template>


				<view class="xilu_rate_link">
					<view class="flex-box">
						<view class="colf fw500 w90 fs30 lh42 mr20">专业度</view>
						<htz-rate v-model="ceshi1" checkedHref="../../static/images/rate_sc.png"
							disHref="../../static/images/rate_uc.png" :size="48" :gutter="30"
							@change="ceshiChange"></htz-rate>
						<view class="colf fs28 lh40">{{ceshi1}}分</view>
					</view>
					<view class="flex-box mt40">
						<view class="colf fw500 fs30 w90 lh42 mr20">亲和力</view>
						<htz-rate v-model="ceshi2" checkedHref="../../static/images/rate_sc.png"
							disHref="../../static/images/rate_uc.png" :size="48" :gutter="30"
							@change="ceshiChange2"></htz-rate>
						<view class="colf fs28 lh40">{{ceshi2}}分</view>
					</view>
					<view class="flex-box mt40">
						<view class="colf fw500 fs30 lh42 w90 mr20">印象</view>
						<htz-rate v-model="ceshi3" checkedHref="../../static/images/rate_sc.png"
							disHref="../../static/images/rate_uc.png" :size="48" :gutter="30"
							@change="ceshiChange3"></htz-rate>
						<view class="colf fs28 lh40">{{ceshi3}}分</view>
					</view>
					<view class="xilu_textarea_link mt50">
						<textarea v-model="content" placeholder="来评价一下您的想法吧~" placeholder-class="col6"></textarea>
						<!-- <view class="tr fs22 colb7 mt30 pr20"><text class="col2">0</text>/30</view> -->
					</view>
				</view>

			</view>
		</view>
	</view>
</template>

<script>
	const app = getApp();
	var eventChannel = null;
	import htzRate from '@/components/htz-rate/htz-rate.vue'
	export default {
		components: {
			htzRate,
		},
		data() {
			return {
				ceshi1: 3,
				ceshi2: 3,
				ceshi3: 3,
				info: '',
				content: ''
			}
		},
		methods: {
			ceshiChange(val) {
				console.log('你选择了' + val + '分');

			},
			ceshiChange2(val) {
				console.log('你选择了' + val + '分');

			},
			ceshiChange3(val) {
				console.log('你选择了' + val + '分');

			},
			//打开地图
			openLocation(lat, lng) {
				this.$api.openLocation(lat, lng);
			},
			//订单详情
			getInfos(id) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/order/getDetail',
					data: {
						id: id
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.info = res.data.info;
					}
				}).catch(error => {
					console.log('orderError', error);
				})
			},
			//提交评价
			comment_post() {
				let _this = this;
				if (!_this.content) {
					this.$api.toast('请输入 评价内容');
				} else {
					this.$http({
						url: '/addons/xilufitness/order/commentOrder',
						data: {
							order_id: _this.info.id || 0,
							content: _this.content,
							profession_star: _this.ceshi1,
							affinity_star: _this.ceshi2,
							impression_star: _this.ceshi3
						},
						method: 'post'
					}).then(res => {
						if (res.code == 1) {
							this.$api.toast('评价成功');
							this.$api.back(2000);
							eventChannel.emit('reloadOrder', {});
						} else {
							this.$api.modal('温馨提示', res.msg, function() {}, false)
						}
					}).catch(error => {
						console.log('commentError', error);
					})
				}
			}
		},
		onLoad(options) {
			eventChannel = this.getOpenerEventChannel();
			this.getInfos(options.id || 0);
		},
		onShareAppMessage() {
		
		}
	}
</script>

<style lang="scss" scoped>
	.page-foot {
		background-color: #0F1011;
	}

	.container {
		background-color: #0F1011;
	}

	.xilu {
		background-color: #0F1011;

		&_info {
			width: 700rpx;
			padding: 20rpx 30rpx 30rpx;
			background: #292B2C;
			border-radius: 20rpx;

			&_cover {
				width: 150rpx;
				height: 150rpx;
				border-radius: 20rpx;
				display: block;
			}
		}

		&_rate_link {
			margin-top: 20rpx;
			padding: 40rpx 40rpx 30rpx 30rpx;
			width: 700rpx;
			background: #292B2C;
			box-shadow: 0rpx 4rpx 20rpx 0rpx rgba(183, 189, 202, 0.1);
			border-radius: 20rpx;
		}

		&_foot_nav {
			padding: 0 30rpx 40rpx;
		}

		&_textarea_link {
			width: 630rpx;
			height: 429rpx;
			background: #0F1011;
			border-radius: 20rpx;

			textarea {
				width: 100%;
				height: 335rpx;
				color: #fff;
				padding: 30rpx;
				font-size: 30rpx;
			}
		}
	}

	.w90 {
		width: 90rpx;
	}

	.colb7 {
		color: #B7B7B7;
	}

	.btn1 {
		height: 90rpx;
		line-height: 90rpx;
		border-radius: 20rpx;
		font-size: 30rpx;
		font-weight: 400;
		color: #0E0E0F;
	}
</style>
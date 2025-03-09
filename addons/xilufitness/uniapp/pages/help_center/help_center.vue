<template>
	<view class="xilu">
		<view class="container">
			<view class="p30">
				<template v-if="list.length > 0">
					<view class="xilu_list_item" v-for="(vo,index) in list" @click="detail(vo.id)">
						<view class="fs34 fw500 colf lh30">{{vo.title || ''}}</view>
						<view class="flex mt20">
							<view class="m-ellipsis-l2 flex-grow-1 fs32 col9 lh46 pr50">{{vo.description || ''}}</view>
							<image src="@/static/images/xilu_arrow_gray_big.png" mode="widthFix" class="ico30 mt6">
							</image>
						</view>
					</view>
				</template>
				<template v-else>
					<empty-data :tips="'暂无数据'" :lineHeight="300"></empty-data>
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
				list: []
			}
		},
		methods: {
			//获取数据
			getLists(is_type=1) {
				let _this = this;
				this.$http({
					url: 'addons/xilufitness/cms/getHelps',
					method: 'get',
					data:{
						is_type:is_type
					}
				}).then(res => {
					if (res.code == 1) {
						_this.list = res.data.list || [];
					}
				}).catch(error => {
					console.log('helpsErros', error);
				});
			},
			//数据详情
			detail(id) {
				this.$api.navigate('../about_us/about_us?id=' + id);
			}
		},
		onLoad(options) {
			this.getLists((options.is_type || 1));
		},
		onShareAppMessage() {
		
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_list_item {
			width: 690rpx;
			margin-bottom: 30rpx;
			background: #404243;
			border-radius: 15rpx;
			padding: 30rpx;
		}

		.pr50 {
			padding-right: 50rpx;
		}
	}
</style>
<template>
	<view class="xilu">
		<view class="container">
			<view class="ptb40 plr30 fs32 col9 lh44" v-if="info">
				<mp-html :content="info.content"></mp-html>
			</view>
		</view>
	</view>
</template>

<script>
	const app = getApp();
	export default {
		data() {
			return {
				info: null
			}
		},
		methods: {
			//获取详情
			getDetail(id, is_type) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/cms/detail',
					data: {
						id: id || 0,
						is_type: is_type || 0
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.info = res.data.info;
						_this.$api.setNavigateTitle(res.data.info.title || '详情');
					}
				}).catch(error => {
					console.log('cmsDetail', error);
				})
			}
		},
		onLoad(options) {
			this.getDetail((options.id || 0),(options.is_type || 0));
		},
		onShareAppMessage() {
		
		}
	}
</script>

<style>

</style>
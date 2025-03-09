<template>
	<view class="empty-data" :style="[{'line-height':lineHeight + 'rpx'}]">{{tips || '暂无数据'}}</view>
</template>

<script>
	export default {
		name: "empty-data",
		data() {
			return {

			};
		},
		props: {
			tips: [String],
			lineHeight: [Number]
		},
		methods: {},
		created() {
			
		}
	}
</script>

<style lang="scss" scoped>
	.empty-data {
		text-align: center;
		line-height: 150rpx;
		color:#999999;
	}
</style>
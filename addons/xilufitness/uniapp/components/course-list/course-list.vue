<template>
	<view>
		<template v-for="(vo,keys) in list">
			<view class="xilu_list_item" @tap="courseDetail(vo.id,vo.course_type)">
				<image :src="vo.course.xilufitness_urls.thumb_image" mode="aspectFill" class="xilu_list_item_cover">
				</image>
				<view class="pt10 plr20">
					<view class="m-ellipsis fs30 fw500 colf lh42">{{vo.course.title || ''}}</view>
					<view class="mt10 fs24 col2 lh32">{{vo.start_at}}～{{vo.end_at}}</view>
					<view class="m-ellipsis mt10 fs24 col9 lh32">{{vo.course.lable_list.join('|')}}</view>
					<view class="flex-box mt10">
						<view class="flex-grow-1 m-ellipsis"><text
								class="col2 fs34 fw500 lh34 pr10">¥{{vo.course_price || 0.00}}</text><text
								class="tdl fs20 col9 lh34" v-if="vo.market_price > 0">¥{{vo.market_price}}</text></view>
						<view class="fs20 col2" v-if="vo.is_plan == 1">{{vo.order_count || 0}}<text
								class="col9">/{{vo.sign_count}}人</text></view>
						<view class="fs20 col2" v-else-if="vo.is_plan == 2">可排队</view>
						<view class="fs20 col9" v-else-if="vo.is_plan == 3">已开始</view>
						<view class="fs20 col9" v-else-if="vo.is_plan == 4">已售罄</view>
					</view>
				</view>
			</view>
		</template>


	</view>
</template>

<script>
	export default {
		name: "course-list",
		data() {
			return {

			};
		},
		props: {
			list: [Array]
		},
		methods: {
			//课程详情
			courseDetail(id, is_type) {
				this.$api.navigate('../../pages/group_lessons_info/group_lessons_info?id=' + id + '&is_type=' + is_type)
			}
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_list_item:nth-of-type(2n) {
			margin-right: 0;
		}

		&_list_item {
			width: 340rpx;
			height: 545rpx;
			background: #292B2C;
			border-radius: 20rpx;
			margin-top: 20rpx;
			margin-right: 20rpx;
			display: inline-block;
			vertical-align: top;

			&_cover {
				width: 100%;
				height: 340rpx;
				display: block;
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
	}
</style>
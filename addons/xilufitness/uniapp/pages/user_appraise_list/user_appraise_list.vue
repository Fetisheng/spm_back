<template>
	<view class="xilu">
		<view class="container">
			<view class="ptb25 plr30">
				<template v-if="list.length > 0">
					<view class="xilu_item" v-for="(vo,keys) in list">
						<view class="flex">
							<image :src="vo.user.xilufitness_urls.avatar || '../../static/images/avatar.png' "
								mode="aspectFill" class="xilu_item_head"></image>
							<view class="flex-grow-1 plr30">
								<view class="m-ellipsis fs30 fw500 colf lh30">{{vo.user.nickname || ''}}</view>
								<view class="flex-box mt10">
									<image src="@/static/images/xilu_star.png" mode="aspectFill" class="ico24"></image>
									<view class="col2 pl10 fs24 lh34 mt5">{{vo.star || 0}}分</view>
								</view>
							</view>
							<view class="fs24 col89 lh24">{{vo.createtime_txt || ''}}</view>
						</view>
						<view class="mt10 fs30 col9 lh44">
							{{vo.content || ''}}
						</view>
					</view>
				</template>
				<template v-else>
					<empty-data :tips="'暂无评论'" :lineHeight="300"></empty-data>
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
				page: 1,
				total_count: 0,
				list: [],
				course_type: 0,
				course_camp_id: 0
			}
		},
		methods: {
			//获取数据
			getLists(course_camp_id, course_type, shop_id) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/course/getCommentList',
					data: {
						id: course_camp_id,
						course_type: course_type,
						shop_id: shop_id,
						page: _this.page
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						if (_this.page > 1) {
							_this.list.push(...res.data.list);
						} else {
							_this.list = res.data.list;
						}
						_this.total_count = res.data.total_count || 0;
					}
				}).catch(error => {
					console.log('CommentListError', error);
				});
			}
		},
		onLoad(options) {
			this.course_camp_id = options.course_camp_id || 0;
			this.course_type = options.course_type || 0;
			this.shop_id = options.shop_id || 0;
			this.getLists((options.course_camp_id || 0), (options.course_type || 0), (options.shop_id || 0));
		},
		onReachBottom() {
			if (this.total_count > this.list.length) {
				this.page = this.page + 1;
				this.getLists(this.course_camp_id, this.course_type, this.shop_id);
			}
		},
		onShareAppMessage() {
		
		}
	}
</script>

<style scoped lang="scss">
	.xilu {
		&_item {
			width: 700rpx;
			padding: 20rpx 30rpx;
			background: #292B2C;
			border-radius: 20rpx;

			&_head {
				width: 75rpx;
				height: 75rpx;
				border-radius: 50%;
				display: block;
			}
		}

		&_item+&_item {
			margin-top: 30rpx;
		}
	}

	.ico24 {
		display: block;
	}
</style>
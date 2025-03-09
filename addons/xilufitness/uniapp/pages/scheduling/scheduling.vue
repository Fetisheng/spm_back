<template>
	<view class="xilu">
		<view class="page-head bg-normal">
			<view class="xilu_tab_nav">
				<view class="xilu_tab_nav_link" :class="nindex==1?'active':''" @click="chooseNav(1)">团课</view>
				<view class="xilu_tab_nav_link" :class="nindex==2?'active':''" @click="chooseNav(2)">私教</view>
				<view class="xilu_tab_nav_link" :class="nindex==3?'active':''" @click="chooseNav(3)">活动</view>
			</view>
		</view>
		<view class="page-foot">
			<Footer :identity="2" :footState="0"></Footer>
		</view>
		<view class="container" style="padding-top: 108rpx;">
			<view class="xilu_date_nav flex-box ml25">
				<template v-for="(vo,index) in timeList">
					<view :class="day_date == vo.day_date ? 'xilu_date_nav_item active' : 'xilu_date_nav_item'"
						@tap="chooseDate(vo.day_date)">
						<view>{{vo.day || ''}}</view>
						<view class="mt5">{{vo.week || ''}}</view>
					</view>
				</template>
			</view>
			<view class="plr25 pb40" v-if="nindex==1">
				<template v-if="list.length > 0">

					<view class="xilu_part1_item" v-for="(vo,index) in list" @click="detail(vo.id,vo.course_type)">
						<view class="flex-box">
							<view class="flex-grow-1 fs34 fw500 colf">{{vo.course.title || ''}}</view>
							<image src="@/static/images/xilu_arrow_white.png" mode="aspectFill" class="ico30"></image>
						</view>
						<view class="mt30 flex-box">
							<image src="@/static/images/xilu_icon21.png" mode="widthFix" class="ico28"></image>
							<view class="w352 plr10 fs28 col2">{{vo.class_time_txt2 || ''}}{{vo.start_at}}～{{vo.end_at}}
							</view>
							<image src="@/static/images/xilu_icon22.png" mode="widthFix" class="ico28"></image>
							<view class="pl10 col9 fs28">总人数<text class="col2">{{vo.user_count || 0}}</text></view>
						</view>
						<view class="mt30 flex-box">
							<image src="@/static/images/xilu_icon23.png" mode="widthFix" class="ico28"></image>
							<view class="flex-grow-1 col9 pl10 fs28 m-ellipsis">{{vo.shop.shop_name || ''}}</view>
						</view>
						<view class="mt30 flex-box" @click.stop="openLocation(vo.shop.lat,vo.shop.lng)">
							<image src="@/static/images/xilu_icon24.png" mode="widthFix" class="ico28"></image>
							<view class="flex-grow-1 plr10 col9 fs28 m-ellipsis">{{vo.shop.address || ''}}</view>
							<image src="@/static/images/xilu_guide.png" mode="widthFix" class="ico30"></image>
						</view>

					</view>

				</template>

				<template v-else>
					<empty-data :tips="'暂无课程排期'" :lineHeight="200"></empty-data>
				</template>

			</view>


			<view class="plr25 pb40" v-if="nindex==2">
				<template v-if="list.length > 0">

					<view class="xilu_part1_item" v-for="(vo,index) in list" @click="detail(vo.id,vo.course_type)">
						<view class="flex-box">
							<view class="flex-grow-1 fs34 fw500 colf">{{vo.course.title || ''}}</view>
							<image src="@/static/images/xilu_arrow_white.png" mode="aspectFill" class="ico30"></image>
						</view>
						<view class="mt30 flex-box">
							<image src="@/static/images/xilu_icon21.png" mode="widthFix" class="ico28"></image>
							<view class="w352 plr10 fs28 col2">{{vo.class_time_txt2 || ''}} {{vo.start_at || ''}}～{{vo.end_at || ''}}
							</view>
							<image src="@/static/images/xilu_icon22.png" mode="widthFix" class="ico28"></image>
							<view class="pl10 col9 fs28">总人数<text class="col2">{{vo.user_count || 0}}</text></view>
						</view>
						<view class="mt30 flex-box">
							<image src="@/static/images/xilu_icon23.png" mode="widthFix" class="ico28"></image>
							<view class="flex-grow-1 col9 pl10 fs28 m-ellipsis">{{vo.shop.shop_name || ''}}</view>
						</view>
						<view class="mt30 flex-box" @click.stop="openLocation(vo.shop.lat,vo.shop.lng)">
							<image src="@/static/images/xilu_icon24.png" mode="widthFix" class="ico28"></image>
							<view class="flex-grow-1 plr10 col9 fs28 m-ellipsis">{{vo.shop.address || ''}}</view>
							<image src="@/static/images/xilu_guide.png" mode="widthFix" class="ico30"></image>
						</view>

					</view>

				</template>

				<template v-else>
					<empty-data :tips="'暂无课程排期'" :lineHeight="200"></empty-data>
				</template>

			</view>


			<view class="plr25 pb40" v-if="nindex==3">
				<template v-if="list.length > 0">
					<view class="xilu_part1_item" v-for="(vo,index) in list" @click="campDetail(vo.id,vo.work_camp.id)">
						<view class="flex-box">
							<view class="flex-grow-1 fs34 fw500 colf">{{vo.camp.title || ''}}</view>
							<image src="@/static/images/xilu_arrow_white.png" mode="aspectFill" class="ico30"></image>
						</view>
						<view class="mt30 flex-box">
							<image src="@/static/images/xilu_icon21.png" mode="widthFix" class="ico28"></image>
							<view class="w352 plr10 fs28 col2">{{vo.day_date || ''}}
								{{vo.day_start_at || ''}}～{{vo.day_end_at || ''}}
							</view>
							<image src="@/static/images/xilu_icon22.png" mode="widthFix" class="ico28"></image>
							<view class="pl10 col9 fs28">总人数<text class="col2">{{vo.user_count || 0}}</text></view>
						</view>
						<view class="mt30 flex-box">
							<image src="@/static/images/xilu_icon23.png" mode="widthFix" class="ico28"></image>
							<view class="flex-grow-1 col9 pl10 fs28 m-ellipsis">{{vo.shop.shop_name || ''}}</view>
						</view>
						<view class="mt30 flex-box" @click.stop="openLocation(vo.shop.lat,vo.shop.lng)">
							<image src="@/static/images/xilu_icon24.png" mode="widthFix" class="ico28"></image>
							<view class="flex-grow-1 plr10 col9 fs28 m-ellipsis">{{vo.shop.address || ''}}</view>
							<image src="@/static/images/xilu_guide.png" mode="widthFix" class="ico30"></image>
						</view>
					</view>
				</template>
				<template v-else>
					<empty-data :tips="'暂无活动排期'" :lineHeight="200"></empty-data>
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
				tabList: ['团课', '活动', '私教'],
				nindex: 1,
				timeList: [],
				day_date: '',
				page: 1,
				list: [],
				total_count: 0
			}
		},
		methods: {
			chooseNav(e) {
				this.nindex = e;
				this.clearData();
			},
			//选择日期
			chooseDate(day_date) {
				this.day_date = day_date;
				this.clearData();
			},
			//清楚数据
			clearData() {
				this.list = [];
				this.page = 1;
				this.total_count = 0;
				this.getLists();
			},
			//获取数据
			getLists() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/coach/getScheduling',
					data: {
						page: _this.page,
						course_type: _this.nindex,
						day_date: _this.day_date
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
						_this.timeList = res.data.timeList || [];
						_this.day_date = _this.day_date || res.data.day_date;
					}
				}).catch(error => {

				});
			},
			//详情
			detail(id, is_type) {
				this.$api.navigate('../scheduling_info/scheduling_info?id=' + id + '&is_type=' + is_type);
			},
			//活动详情
			campDetail(id, work_camp_id) {
				this.$api.navigate('../scheduling_info/scheduling_info?id=' + id + '&is_type=3' + '&work_camp_id=' +
					work_camp_id);
			},
			//打开地图
			openLocation(lat, lng) {
				this.$api.openLocation(lat, lng);
			}
		},
		onLoad() {
			this.getLists();
		},
		onReachBottom() {
			let list = this.list;
			let total_count = this.total_count;
			if (list.length > total_count) {
				this.page = this.page + 1;
				this.getLists();
			}
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_tab_nav {
			height: 108rpx;
			line-height: 108rpx;
			padding-left: 32rpx;

			&_link {
				margin-right: 40rpx;
				font-size: 34rpx;
				font-weight: 400;
				color: #FFFFFF;
				display: inline-block;
				vertical-align: top;
			}

			&_link.active {
				font-weight: 500;
				color: #FFCF00;
			}
		}

		&_date_nav {
			width: 700rpx;
			height: 145rpx;
			background: #404243;
			border-radius: 20rpx;

			&_item {
				width: 58rpx;
				height: 110rpx;
				font-size: 28rpx;
				font-weight: 400;
				color: #FFFFFF;
				margin-left: 42rpx;
				line-height: 40rpx;
				text-align: center;
				padding-top: 13rpx;
			}

			&_item:first-child {
				margin-left: 16rpx;
			}

			&_item.active {
				background: #FFCF00;
				border-radius: 29rpx;
				color: #101010;
				line-height: 40rpx;
			}
		}

		&_part1_item {
			padding: 30rpx 30rpx 25rpx;
			background: #292B2C;
			border-radius: 20rpx;
			width: 100%;
			margin-top: 30rpx;
		}
	}

	.w352 {
		width: 352rpx;
	}
</style>
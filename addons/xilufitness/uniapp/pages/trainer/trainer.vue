<template>
	<view class="xilu">
		<view class="page-head bg-normal">
			<hx-navbar :config="config">
				<block slot="max">
					<view class="xilu_head_search flex-box">
						<picker mode="selector" :value="c_index" :range="province_list" range-key="name"
							@change="changeCity">
							<view class="flex-box">
							<image src="@/static/images/xilu_icon_address_white.png" mode="widthFix" class="ico26">
							</image>
							<view class="plr10 fs28 colf fw500">
								{{province_list[c_index]['name'] || (city_name || '未知')}}
							</view>
							<image src="@/static/images/xilu_icon_arrowdown_white.png" mode="widthFix" class="ico17">
							</image>
							</view>
						</picker>
						<view class="xilu_search_sq"></view>
						<input type="text" class="flex-grow-1 fs26 colf pr10" placeholder="请输入" placeholder-class="col9"
							confirm-type="search" @confirm="input_keyword($event)">
						<image src="@/static/images/xilu_icon_search.png" mode="widthFix" class="ico28"></image>
					</view>
				</block>
			</hx-navbar>
			<scroll-view scroll-x="true" class=" xilu_head_scroll">
				<view class="xilu_head_scroll_item" :class="cate_pid==0?'active':''" @click="chooseHaedNav(0)">全部</view>
				<view class="xilu_head_scroll_item" :class="cate_pid==vo.id?'active':''"
					v-for="(vo,index) in course_cate_list" @click="chooseHaedNav(vo.id)">{{vo.cate_name || ''}}</view>
			</scroll-view>
			<view class="xilu_class_nav">
				<view class="xilu_class_nav_link" :class="hindex==0?'active':''" @click="chooseClassNav(0)">
					<view>课程</view>
					<image src="@/static/images/xilu_arrow_yellow.png" mode="aspectFill" class="arrow" v-if="hindex==0">
					</image>
					<image src="@/static/images/xilu_arrow_gray.png" mode="aspectFill" class="arrow" v-else>
					</image>
				</view>
				<view class="xilu_class_nav_link" :class="hindex==1?'active':''" @click="chooseClassNav(1)">
					<view>门店</view>
					<image src="@/static/images/xilu_arrow_yellow.png" mode="aspectFill" class="arrow" v-if="hindex==1">
					</image>
					<image src="@/static/images/xilu_arrow_gray.png" mode="aspectFill" class="arrow" v-else>
					</image>
				</view>
				<view class="xilu_class_nav_link" :class="hindex==2?'active':''" @click="chooseCollect(2)">
					<view>收藏的教练</view>
				</view>
			</view>
		</view>
		<view class="page-foot">
			<Footer :identity="1" :footState="2"></Footer>
		</view>
		<view class="container" :style="{paddingTop:`${barHeight + 80}px`}">
			<view class="pb30 plr25">
				<template v-if="list.length > 0">

					<template v-for="(vo,keys) in list">
						<view class="flex-box ptb30" @tap="shop_detail(vo.id)">
							<view class="flex-grow-1 fs30 cola">{{vo.shop_name || ''}} <distance-format
									:distance="vo.distance"></distance-format></view>
							<view class="fs30 cola">门店详情</view>
							<image src="@/static/images/xilu_arrow_right.png" mode="widthFix" class="ico12 ml10">
							</image>
						</view>
						<view class="xilu_box1">

							<template v-if="vo.coach_list.length > 0">
								<view class="xilu_box1_item flex-box" v-for="(vv,kk) in vo.coach_list"
									@tap="coach_info(vv.id,vo.id)">
									<image :src="vv.xilufitness_urls.coach_avatar || '../../static/images/avatar.png' "
										mode="aspectFill" class="xilu_box1_cover"></image>
									<view class="flex-grow-1 plr20">
										<view class="flex-box">
											<view class="fs30 fw500 colf lh42">{{vv.coach_name || ''}}</view>
											<image :src="vv.group_image" mode="widthFix" class="xilu_label ml20">
											</image>
										</view>
										<view class="mt25 fs30 colf lh42">{{vv.lable_list.join(' | ')}}</view>
									</view>
									<view class="xilu_btn">预约</view>
								</view>
							</template>

							<template v-else>
								<empty-data :tips="'暂无教练'" :lineHeight="100"></empty-data>
							</template>




						</view>
					</template>

				</template>

				<template v-else>
					<empty-data :tips="'暂无教练'" :lineHeight="400"></empty-data>
				</template>


			</view>
		</view>

		<u-popup :show="show" mode="bottom" @close="closePop()">
			<view class="xilu_popup">
				<view class="xilu_popup_head">
					<view class="xilu_popup_tab flex-box flex-center" :class="hindex==0?'active':''"
						@click="chooseHead(0)">
						<view>课程</view>
						<image src="@/static/images/xilu_arrow_yellow.png" mode="aspectFill" class="arrow"
							v-if="hindex==0"></image>
						<image src="@/static/images/xilu_icon_arrowdown_white.png" mode="aspectFill" class="arrow"
							v-else>
						</image>
					</view>
					<view class="xilu_popup_tab flex-box flex-center" :class="hindex==1?'active':''"
						@click="chooseHead(1)">
						<view>门店</view>
						<image src="@/static/images/xilu_arrow_yellow.png" mode="aspectFill" class="arrow"
							v-if="hindex==1"></image>
						<image src="@/static/images/xilu_icon_arrowdown_white.png" mode="aspectFill" class="arrow"
							v-else>
						</image>
					</view>

				</view>
				<view class="xilu_popup_scroll flex" v-if="hindex==0">
					<scroll-view class="xilu_popup_left" scroll-y>
						<view class="xilu_left_item" :class="cate_pid == 0?'active':''"
							@tap="chooseLeft(0,'course_cate')">全部
						</view>

						<view class="xilu_left_item" :class="cate_pid == vo.id ?'active':''"
							v-for="(vo,index) in course_cate_list" @click="chooseLeft(vo.id,'course_cate')">
							{{vo.cate_name}}
						</view>

					</scroll-view>
					<scroll-view class="xilu_popup_right flex-grow-1" scroll-y>

						<view class="xilu_right_item" :class="cate_id==0?'active':''"
							@click="chooseRight(0,'course_cate')">全部</view>

						<view class="xilu_right_item" :class="cate_id==vo.id?'active':''"
							v-for="(vo,index) in course_cate_list_two" @click="chooseRight(vo.id,'course_cate')">
							{{vo.cate_name}}
						</view>
					</scroll-view>
				</view>
				<view class="xilu_popup_scroll flex" v-if="hindex==1">
					<scroll-view class="xilu_popup_left" scroll-y>
						<view class="xilu_left_item" :class=" (province_id == 0) ?'active':''"
							@click="chooseLeft(0,'province')">全部</view>

						<view class="xilu_left_item"
							:class="(province_id == vo.id || province_id == vo.province_id)?'active':''"
							v-for="(vo,index) in province_list" @click="chooseLeft(vo,'province')">{{vo.name || ''}}
						</view>

					</scroll-view>
					<scroll-view class="xilu_popup_right flex-grow-1" scroll-y>
						<view class="xilu_right_item" :class="(city_id == 0 && area_id == 0) ?'active':''"
							@click="chooseRight(0,'city')">全部</view>

						<view class="xilu_right_item" :class="(city_id == vo.id || area_id == vo.id) ? 'active':''"
							v-for="(vo,index) in city_list" @click="chooseRight(vo,'city')">{{vo.name || ''}}</view>
					</scroll-view>
				</view>

				<view class="p30 flex-box">
					<view @tap="resetData()" class="xilu_btn_reset">重置</view>
					<view @tap="clearData()" class="xilu_btn_sure">确认</view>
				</view>
			</view>
		</u-popup>


	</view>
</template>

<script>
	const app = getApp();
	export default {
		data() {
			return {
				config: {
					back: false,
					maxSlot: true,
					backgroundColor: [1, '#0F1011'],
					statusBarFontColor: ['#ffffff']
				},
				cindex: -1,
				statusBarHeight: null,
				barHeight: null,

				course_cate_list: [],
				course_cate_list_two: [],
				cate_pid: 0,
				cate_id: 0,
				list: [],
				page: 1,
				total_count: 0,
				city_id: 0,
				province_id: 0,
				area_id: 0,
				city_name: '',
				keywords: '',
				province_list: [],
				city_list: [],
				c_index: '',
				hindex: -1,
				show: false
			}
		},
		methods: {
			//选择课程一级分类
			chooseHaedNav(id) {
				this.cate_pid = id;
				this.getCateList(id);
				this.hindex = -1;
				this.clearData();
			},
			chooseClassNav(e) {
				this.hindex = e;
				this.show = true;
			},
			//收藏的教练
			chooseCollect(e) {
				this.hindex = e;
				this.cate_pid = 0;
				this.clearData();
			},
			//输入关键词
			input_keyword(e) {
				this.keywords = e.detail.value;
				this.clearData();
			},
			//关闭弹窗
			closePop() {
				this.show = false;
			},
			//选择城市
			changeCity(e) {
				let city_list = this.province_list || [];
				let c_index = e.detail.value || '';
				this.c_index = c_index;
				if (city_list[c_index]) {
					app.globalData.cityInfo = city_list[c_index];
					this.city_id = city_list[c_index]['id'] || 0;
					this.province_id = city_list[c_index]['province_id'] || 0;
					this.city_name = city_list[c_index]['name'] || '';
					this.clearData();
				}
			},
			chooseHead(e) {
				if (e == 0 && this.course_cate_list.length == 0) {
					this.getCateList(0);
				} else if (e == 1 && this.province_list.length == 0) {
					this.getCityList(0);
				}
				this.hindex = e;
				this.cindex = e;
			},
			//选择搜索条件左侧
			chooseLeft(id, is_type) {
				if (is_type == 'course_cate') {
					// 课程一级分类
					this.cate_pid = id;
					if (id == 0) {
						this.course_cate_list_two = [];
					}
					this.cate_id = 0;
					this.getCateList(id)
				} else if (is_type == 'province') {
					if (id.province_id) {
						this.province_id = id.province_id || 0;
						this.city_id = id.id || 0;
					} else {
						this.province_id = id.id || 0;
					}
					this.city_list = [];
					this.area_id = 0;
					//市区数据
					this.getCityList(id.id || 0);

				}
			},
			//右侧数据显示
			chooseRight(id, is_type) {
				if (is_type == 'course_cate') {
					//课程子分类
					this.cate_id = id;
				} else if (is_type == 'city') {
					//市/区数据
					if (this.province_id) {
						this.city_id = id.pid || 0;
						this.area_id = id.id || 0;
					} else {
						this.city_id = id.id;
						this.area_id = 0;
					}

				}
			},
			/**
			 * 获取城市列表
			 */
			getCityList(pid = 0) {
				let _this = this;
				if (app.globalData.cityList.length > 0 && pid == 0) {
					this.province_list = app.globalData.cityList;
					return false;
				} else {
					this.$http({
						url: '/addons/xilufitness/home/getCityList',
						data: {
							pid: pid
						},
						method: 'get'
					}).then(res => {
						if (res.code == 1) {
							if (pid == 0) {
								_this.province_list = res.data.list || [];
							} else {
								_this.city_list = res.data.list || [];
							}
							if (pid == 0 && app.globalData.cityList.length == 0) {
								app.globalData.cityList = res.data.list || [];
							}

						}
					}).catch(error => {
						console.log('cityListError', error);
					})
				}
			},
			//清空数据
			clearData() {
				let _this = this;
				_this.show = false;
				_this.page = 1;
				_this.list = [];
				_this.total_count = 0;
				_this.getLists();
			},
			//获取课程分类
			getCateList(pid) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/course_cate/index',
					data: {
						pid: pid
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						if (pid == 0) {
							_this.course_cate_list = res.data.list || [];
						} else {
							_this.course_cate_list_two = res.data.list || [];
						}
					}
				}).catch(error => {
					console.log('cateError', error);
				});
			},

			//获取门店教练列表
			getLists() {
				let _this = this;
				let url = '';
				if (this.hindex == 2) {
					url = '/addons/xilufitness/course/getCollectCoach';
				} else {
					url = '/addons/xilufitness/coach/index';
				}
				this.$http({
					url: url,
					data: {
						lat: app.globalData.lat,
						lng: app.globalData.lng,
						page: _this.page,
						cate_pid: _this.cate_pid,
						cate_id: _this.cate_id,
						province_id: _this.province_id,
						city_id: _this.city_id,
						area_id: _this.area_id,
						keywords: _this.keywords
					},
					method: 'POST'
				}).then(res => {
					if (res.code == 1) {
						if (_this.page == 1) {
							_this.list = res.data.list;
						} else {
							_this.list.push(...res.data.list)
						}
						_this.total_count = res.data.total_count;
					}
				}).catch(error => {
					console.log('coachError', error);
				});
			},
			//教练详情
			coach_info(id, shop_id) {
				this.$api.navigate("../trainer_info/trainer_info?id=" + id + '&shop_id=' + shop_id)
			},
			//门店详情
			shop_detail(id) {
				this.$api.navigate("../stores_info/stores_info?id=" + id)
			},
			//重置数据
			resetData() {
				this.cate_id = 0;
				this.cate_pid = 0;
				this.hindex = -1;
				this.province_id = 0;
				this.city_id = 0;
				this.area_id = 0;
				this.clearData();
			}

		},
		onLoad() {
			let city_info = app.globalData.cityInfo;
			this.statusBarHeight = uni.getSystemInfoSync().statusBarHeight;
			this.barHeight = 44 + this.statusBarHeight;
			this.city_id = city_info.id || 0;
			this.city_name = city_info.name || '';
			this.getCityList(0);
			this.getCateList(0);
			this.getLists();
		},
		onReachBottom() {
			if (this.total_count > this.list.length) {
				this.page = this.page + 1;
				this.getLists();
			}
		},
		onShareAppMessage() {
		
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_head_search {
			width: 512rpx;
			height: 64rpx;
			background: #292B2C;
			border-radius: 32rpx;
			padding-left: 20rpx;
			padding-right: 30rpx;
		}

		&_search_sq {
			width: 1px;
			height: 24rpx;
			background: #666666;
			margin-left: 20rpx;
			margin-right: 10rpx;
		}

		&_head_scroll {
			height: 108rpx;
			line-height: 108rpx;

			&_item {
				font-size: 34rpx;
				font-weight: 400;
				color: #FFFFFF;
				line-height: 108rpx;
				margin-right: 40rpx;
				display: inline-block;
				vertical-align: top;
			}

			&_item.active {
				font-size: 34rpx;
				font-weight: 500;
				color: #FFCF00;
			}

			&_item:first-child {
				margin-left: 32rpx;
			}
		}

		&_class_nav {
			margin-bottom: 30rpx;
			padding-left: 25rpx;

			&_link {
				display: inline-flex;
				align-items: center;
				height: 52rpx;
				background: #292B2C;
				border-radius: 26rpx;
				padding: 0 30rpx;
				font-size: 30rpx;
				font-weight: 400;
				color: #999999;
			}



			&_link.active {
				border: 1px solid #FFCF00;
				color: #FFCF00;
			}

			&_link+&_link {
				margin-left: 18rpx;
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

		&_tips {
			display: inline-block;
			height: 39rpx;
			line-height: 39rpx;
			background: linear-gradient(90deg, #FFD26A 0%, #FFB94E 100%);
			border-radius: 19rpx;
			padding: 0 15rpx;
			font-size: 24rpx;
			font-weight: 400;
			color: #000000;
			margin-left: 20rpx;
		}

		&_btn {
			width: 118rpx;
			height: 57rpx;
			line-height: 57rpx;
			text-align: center;
			font-size: 28rpx;
			font-weight: 400;
			color: #0E0E0F;
			background: #FFCF00;
			border-radius: 10rpx;
		}

		&_label {
			width: 161rpx;
			height: 38rpx;
		}


		&_popup {
			width: 100%;
			background: #292B2C;

			&_head {
				width: 100%;
				height: 107rpx;
				background: #404243;
				padding: 29rpx 25rpx 25rpx;
				white-space: nowrap;
				overflow-x: auto;
			}

			&_tab {
				width: 145rpx;
				height: 52rpx;
				line-height: calc(52rpx - 2px);
				text-align: center;
				font-size: 30rpx;
				display: inline-flex;
				color: #FFFFFF;
				border-radius: 26rpx;
				border: 1px solid #FFFFFF;
				vertical-align: top;
			}

			&_tab+&_tab {
				margin-left: 18rpx;
			}

			&_tab.active {
				width: 145rpx;
				height: 52rpx;
				background: rgba(246, 204, 25, 0.2);
				border-radius: 26rpx;
				border: 1px solid #FFCF00;
				font-size: 30rpx;
				color: #FFCF00;
			}

			&_scroll {
				width: 100%;
				height: 828rpx;
			}

			&_left {
				height: 828rpx;
				width: 280rpx;
				border-right: 1px solid #666666;
				background: #2F3032;
				padding-bottom: 1px;
			}

			&_right {
				height: 828rpx;
				background: #292B2C;
				padding-left: 28rpx;
			}
		}

		.arrow {
			width: 15rpx;
			height: 8rpx;
			margin-left: 10rpx;
		}

		&_left_item:last-child {
			padding-bottom: 20rpx;
			height: 100rpx;
		}

		&_left_item {
			height: 80rpx;
			line-height: 80rpx;
			padding-left: 35rpx;
			font-size: 30rpx;
			font-weight: 500;
			color: #FFFFFF;
			position: relative;
			margin-top: 24rpx;

			&.active {
				font-size: 30rpx;
				font-weight: 500;
				color: #FFCF00;
			}

			&.active::before {
				content: '';
				border-radius: 0rpx 100rpx 100rpx 0rpx;
				background: #FFCF00;
				position: absolute;
				top: 0;
				left: 0;
				width: 5rpx;
				height: 80rpx;
			}
		}

		&_right_item:first-child {
			margin-top: 30rpx;
		}

		&_right_item {
			width: 430rpx;
			height: 80rpx;
			line-height: calc(80rpx - 2px);
			text-align: center;
			border-radius: 15rpx;
			border: 1px solid #999999;
			font-size: 30rpx;
			font-weight: 400;
			color: #FFFFFF;
			margin-top: 20rpx;

			&.active {
				font-size: 30rpx;
				font-weight: 400;
				color: #FFCF00;
				background: rgba(246, 204, 25, 0.2);
				box-shadow: 0rpx 4rpx 20rpx 5rpx rgba(183, 189, 202, 0.05);
				border: 1px solid #FFCF00;
			}
		}

		&_btn_reset {
			width: 230rpx;
			height: 90rpx;
			line-height: calc(90rpx - 2px);
			border-radius: 20rpx;
			text-align: center;
			font-size: 30rpx;
			font-weight: 400;
			color: #FFFFFF;
			border: 1px solid #979797;
		}

		&_btn_sure {
			width: 430rpx;
			height: 90rpx;
			line-height: 90rpx;
			font-size: 30rpx;
			font-weight: 400;
			color: #0E0E0F;
			text-align: center;
			background: #FFCF00;
			border-radius: 20rpx;
			margin-left: 30rpx;
		}

		&_time_nav {
			padding-top: 24rpx;
			padding-left: 25rpx;
			padding-right: 25rpx;
			padding-bottom: 10rpx;
		}

		&_time_link {
			width: 300rpx;
			height: 80rpx;
			line-height: calc(80rpx - 2px);
			text-align: center;
			font-size: 30rpx;
			font-weight: 400;
			color: #999999;
			border-radius: 15rpx;
			border: 1px solid #666666;
		}

		&_sq {
			width: 29rpx;
			height: 1px;
			background: #EEEEEE;
			border-radius: 45rpx;
			margin-left: 35rpx;
			margin-right: 35rpx;
		}

		&_time_scroll {
			height: 710rpx;
			padding: 0 25rpx 20rpx;

			&_item {
				width: 700rpx;
				height: 80rpx;
				line-height: calc(80rpx - 2px);
				text-align: center;
				font-size: 30rpx;
				font-weight: 400;
				color: #FFFFFF;
				border-radius: 15rpx;
				border: 1px solid #666666;
				margin-top: 20rpx;
			}

			&_item.active {
				background: rgba(246, 204, 25, 0.2);
				box-shadow: 0rpx 4rpx 20rpx 5rpx rgba(183, 189, 202, 0.05);
				border: 1rpx solid #FFCF00;
				color: #FFCF00;
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

	.arrow {
		width: 15rpx;
		height: 8rpx;
		margin-left: 10rpx;
	}
</style>
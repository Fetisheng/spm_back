<template>
	<view class="xilu">
		<hx-navbar :config="config">
			<block slot="left">
				<picker mode="selector" :value="c_index" :range="city_list" range-key="name" @change="changeCity">
					<view class="left flex-box pl25">
						<image src="@/static/images/xilu_icon_address.png" mode="aspectFill" class="xilu_icon_address">
						</image>
						<view class="fs28 fw500 coldark plr10">{{city_list[c_index]['name'] || (cityInfo.name || '未知')}}
						</view>
						<image src="@/static/images/xilu_icon_arrowdown.png" mode="aspectFill" class="xilu_icon_down">
						</image>
					</view>
				</picker>

			</block>
			<block slot="center">
				<text class="fs36 fw500 col0">首页</text>
			</block>
		</hx-navbar>
		<view class="page-foot">
			<Footer :identity="1" :footState="0"></Footer>
		</view>
		<view class="container">
			<!-- 轮播区域 -->
			<view class="pr">
				<swiper class="xilu_home_swiper pr z2" :indicator-dots="true" :circular="true" :autoplay="true"
					:interval="3000" :duration="1000">
					<swiper-item v-for="(vo,key) in bannerList">
						<image :src="vo.xilufitness_urls.thumb_image" @click.stop="banner_redirect(vo.redirect_url)"
							mode="aspectFill" class="banner"></image>
					</swiper-item>
				</swiper>
				<view class="xilu_swiper_bottom flex-box flex-center">
					<image src="@/static/images/xilu_icon1.png" mode="aspectFill" class="ico24 mid"></image>
					<view class="fs24 fw500 colf pl5 pr40 textshadow">按次付费</view>
					<image src="@/static/images/xilu_icon2.png" mode="aspectFill" class="ico24 mid"></image>
					<view class="fs24 fw500 colf pl5 pr40 textshadow">优质课程</view>
					<image src="@/static/images/xilu_icon3.png" mode="aspectFill" class="ico24 mid"></image>
					<view class="fs24 fw500 colf pl5 pr40 textshadow">专业教练</view>
					<image src="@/static/images/xilu_icon4.png" mode="aspectFill" class="ico24 mid"></image>
					<view class="fs24 fw500 colf pl5 textshadow">高效快乐</view>
				</view>
			</view>
			<!-- 金刚区 -->
			<view class="xilu_router_link_nav flex-box flex-center">
				<view class="xilu_route_link" @tap="moreCourse(1)">
					<image src="@/static/images/xilu_icon5.png" mode="widthFix" class="xilu_route_img"></image>
					<view class="fs28 lh28 colf">团课</view>
				</view>
				<view class="xilu_route_link" @tap="moreCourse(2)">
					<image src="@/static/images/xilu_icon6.png" mode="widthFix" class="xilu_route_img"></image>
					<view class="fs28 lh28 colf">私教</view>
				</view>
				<view class="xilu_route_link" @tap="moreCourse(3)">
					<image src="@/static/images/xilu_icon7.png" mode="widthFix" class="xilu_route_img"></image>
					<view class="fs28 lh28 colf">活动</view>
				</view>
				<view class="xilu_route_link" @tap="toMine()">
					<image src="@/static/images/xilu_icon8.png" mode="widthFix" class="xilu_route_img"></image>
					<view class="fs28 lh28 colf">会员卡</view>
				</view>
				<button class="xilu_route_link" open-type="contact" hover-class="none">
					<image src="@/static/images/xilu_icon27.png" mode="widthFix" class="xilu_route_img"></image>
					<view class="fs28 lh28 colf">客服</view>
				</button>
			</view>
			<!-- 列表1 -->
			<view class="flex-box flex-between plr25">
				<view class="fs36 fw500 colf lh50">门店列表</view>
				<view @click="go_shop_list()" class="xilu_btn_more">查看更多</view>
			</view>
			<scroll-view class="xilu_scroll" scroll-x="true">
				<template v-if="shopList.length > 0">
					<view class="xilu_scroll_item" v-for="(vo,keys) in shopList" @tap="shopDetail(vo.id)">
						<image :src="vo.xilufitness_urls.shop_image" mode="aspectFill" class="xilu_scroll_cover">
						</image>
						<view class="mt20 plr20 fw500 colf fs30 lh42">{{vo.shop_name || ''}}</view>
						<view class="mt10 plr20 flex-box flex-between">
							<view class="xilu_scroll_address m-ellipsis">{{vo.address || ''}}</view>
							<view class="fs24 col9 lh32">
								<distance-format :distance="vo.distance"></distance-format>
							</view>
						</view>
					</view>
				</template>
				<template v-else>
					<empty-data :tips="'暂无门店'" :lineHeight="100">

					</empty-data>
				</template>

			</scroll-view>
			<view class="flex-box flex-between mt70 plr25">
				<view class="fs36 fw500 colf lh50">课程专区</view>
				<view @tap="moreCourse(1)" class="xilu_btn_more">查看更多</view>
			</view>
			<view class="plr25 pt10">
				<template v-if="courseList.length > 0">
					<course-list :list="courseList"></course-list>

				</template>

				<template v-else>
					<empty-data :tips="'暂无课程'" :lineHeight="100"></empty-data>
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
				config: {
					back: false,
					leftSlot: true,
					centerSlot: true,
					backgroundColor: [1, '#ffcf00'],
				},
				bannerList: [], //banner数据
				shopList: [], //门店数据
				courseList: [], //课程数据
				cityInfo: null,
				city_list: [],
				c_index: ''
			}
		},
		onLoad() {
			this.getCityList();
			this.getHomeLocation();
		},
		onShareAppMessage() {

		},
		methods: {
			//选择城市
			changeCity(e) {
				let city_list = this.city_list;
				let c_index = e.detail.value || '';
				this.c_index = c_index;
				if (city_list[c_index]) {
					app.globalData.cityInfo = city_list[c_index];
					this.getHomeData();
				}
			},
			
			/**
			 * 开启定位
			 */
			getHomeLocation() {
				console.log('111');
				let _this = this;
				this.$api.getLocation(function(res) {
					console.log('res', res);
					if (res.latitude && res.longitude) {
						app.globalData.lat = res.latitude;
						app.globalData.lng = res.longitude;
					} else {
						app.globalData.lat = '31.231859';
						app.globalData.lng = '121.486561';
					}
					_this.getHomeData();
					
				});
			},
			/**
			 * 获取城市列表
			 */
			getCityList() {
				let _this = this;
				if (app.globalData.cityList.length > 0) {
					this.city_list = app.globalData.cityList;
					return false;
				} else {
					this.$http({
						url: '/addons/xilufitness/home/getCityList',
						data: {
							pid: 0
						},
						method: 'get'
					}).then(res => {
						if (res.code == 1) {
							_this.city_list = res.data.list || [];
							app.globalData.cityList = res.data.list || [];
						}
					}).catch(error => {
						console.log('cityListError', error);
					})
				}

			},
			/**
			 * 获取首页数据
			 */
			getHomeData() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/home/index',
					data: {
						lat: app.globalData.lat,
						lng: app.globalData.lng,
						city_id: app.globalData.cityInfo ? (app.globalData.cityInfo.id || 0) : 0,
						province_id: app.globalData.cityInfo ? (app.globalData.cityInfo.province_id || 0) : 0
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.bannerList = res.data.bannerList;
						_this.shopList = res.data.shopList;
						_this.courseList = res.data.courseList;
						_this.cityInfo = app.globalData.cityInfo || res.data.cityInfo;
						if (!app.globalData.cityInfo) {
							app.globalData.cityInfo = res.data.cityInfo;
						}
					}
				}).catch(error => {
					console.log('home_error', error);
				})
			},
			//更多门店
			go_shop_list() {
				this.$api.navigate('../stores_list/stores_list');
			},
			//门店详情
			shopDetail(id) {
				this.$api.navigate('../stores_info/stores_info?id=' + id)
			},
			//更多课程
			moreCourse(status) {
				this.$api.navigate('../course_classification/course_classification?status=' + status)
			},
			//课程详情
			courseDetail(id) {
				this.$api.navigate('../group_lessons_info/group_lessons_info?id=' + id)
			},
			//会员卡
			toMine() {
				this.$api.navigate('../mine/mine')
			},
			//banner点击跳转
			banner_redirect(url) {
				if (url) {
					this.$api.navigate(url);
				}
			}
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_icon_address {
			width: 26rpx;
			height: 28rpx;
		}

		&_icon_down {
			width: 17rpx;
			height: 9rpx;
		}

		&_home_swiper {
			width: 750rpx;
			height: 414rpx;

			.banner {
				display: block;
				width: 100%;
				height: 100%;
			}
		}

		&_swiper_bottom {
			position: absolute;
			z-index: 3;
			bottom: 30rpx;
			left: 0;
			right: 0;
		}

		&_router_link_nav {
			margin-top: 50rpx;
			margin-bottom: 70rpx;
		}

		&_route_img {
			width: 107rpx;
			margin-bottom: 5rpx;
		}

		&_route_link {
			width: 107rpx;
			text-align: center;

		}

		&_route_link+&_route_link {
			margin-left: 40rpx;
		}

		&_btn_more {
			font-size: 22rpx;
			font-weight: 400;
			color: #999999;
			width: 146rpx;
			height: 55rpx;
			line-height: calc(55rpx - 2px);
			text-align: center;
			border-radius: 28rpx;
			border: 1px solid #979797;
		}

		&_scroll {
			width: 100%;
			white-space: nowrap;
			height: 368rpx;
			margin-top: 32rpx;
		}

		&_scroll_cover {
			width: 100%;
			height: 253rpx;
			display: block;
			border-radius: 20rpx 20rpx 0 0;
		}

		&_scroll_address {
			width: 383rpx;
			font-size: 24rpx;
			font-weight: 400;
			color: #999999;
			line-height: 33rpx;
		}

		&_scroll_item {
			width: 545rpx;
			height: 368rpx;
			background: #292B2C;
			border-radius: 20rpx;
			display: inline-block;
			vertical-align: top;
			margin-right: 20rpx;
		}

		&_scroll_item:first-child {
			margin-left: 25rpx;
		}

		&_scroll::-webkit-scrollbar {
			display: none;
		}

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
	}
	.textshadow{
		text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
	}
</style>
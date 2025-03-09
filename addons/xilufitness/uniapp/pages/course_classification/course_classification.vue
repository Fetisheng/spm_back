<template>
	<view class="xilu">
		<hx-navbar :config="config">
			<block slot="left">
				<picker mode="selector" :value="c_index" :range="province_list" range-key="name" @change="changeCity">
					<view class="left flex-box pl25">
						<image src="@/static/images/xilu_icon_address.png" mode="aspectFill" class="xilu_icon_address">
						</image>
						<view class="fs28 fw500 coldark plr10">{{province_list[c_index]['name'] || (city_name || '未知')}}
						</view>
						<image src="@/static/images/xilu_icon_arrowdown.png" mode="aspectFill" class="xilu_icon_down">
						</image>
					</view>
				</picker>

			</block>
			<block slot="center">
				<text class="fs36 fw500 col0">课程</text>
			</block>
		</hx-navbar>
		<view class="page-foot">
			<Footer :identity="1" :footState="1"></Footer>
		</view>
		<view class="container">
			<swiper class="xilu_course_swiper pr z2" :indicator-dots="true" :circular="true" :autoplay="true"
				:interval="3000" :duration="1000">
				<swiper-item v-for="(vo,keys) in bannerList">
					<image :src="vo.xilufitness_urls.thumb_image" mode="aspectFill" class="banner"
						@click.stop="banner_redirect(vo.redirect_url)"></image>
				</swiper-item>
			</swiper>
			<view class="xilu_tab_nav">
				<view class="xilu_tab_nav_link" :class="course_type == 1 ? 'active':''" @click="chooseNav(1)">团课</view>
				<view class="xilu_tab_nav_link" :class="course_type == 2 ? 'active':''" @click="chooseNav(2)">私教</view>
				<view class="xilu_tab_nav_link" :class="course_type == 3 ? 'active':''" @click="chooseNav(3)">活动</view>
				<view class="xilu_tab_nav_link" :class="course_type == 4 ? 'active':''" @click="chooseNav(4)">收藏的教练</view>
			</view>
			<template>
				<view class="xilu_class_nav">
					<view class="xilu_class_nav_link" :class="cindex==0?'active':''" @click="chooseClassNav(0)"
						v-if="course_type != 3 && course_type != 4">
						<view>课程</view>
						<image src="@/static/images/xilu_arrow_yellow.png" mode="aspectFill" class="arrow"
							v-if="cindex==0">
						</image>
						<image src="@/static/images/xilu_arrow_gray.png" mode="aspectFill" class="arrow" v-else>
						</image>
					</view>
					<view class="xilu_class_nav_link" :class="cindex==1?'active':''" @click="chooseClassNav(1)">
						<view>门店</view>
						<image src="@/static/images/xilu_arrow_yellow.png" mode="aspectFill" class="arrow"
							v-if="cindex==1">
						</image>
						<image src="@/static/images/xilu_arrow_gray.png" mode="aspectFill" class="arrow" v-else>
						</image>
					</view>
					<view class="xilu_class_nav_link" :class="cindex==2?'active':''" @click="chooseClassNav(2)"
						v-if="course_type != 4">
						<view>时间</view>
						<image src="@/static/images/xilu_arrow_yellow.png" mode="aspectFill" class="arrow"
							v-if="cindex==2">
						</image>
						<image src="@/static/images/xilu_arrow_gray.png" mode="aspectFill" class="arrow" v-else>
						</image>
					</view>
					<!-- <view class="xilu_class_nav_link" :class="course_type == 4?'active':''" @click="chooseNav(4)">
						<view>收藏的教练</view>
					</view> -->
				</view>
				<view class="xilu_date_nav flex-box ml25" v-if="course_type != 4">
					<template v-for="(vo,keys) in classTimeList">
						<view :class="choose_date == vo.day_date ? 'xilu_date_nav_item active' : 'xilu_date_nav_item'"
							@tap="chooseDate(vo.day_date)">
							<view>{{vo.day || ''}}</view>
							<view class="mt5">{{vo.week || ''}}</view>
						</view>
					</template>


				</view>

			</template>

			<template v-if="course_type == 1">
				<!-- 团课 -->

				<view class="pb30 plr25">
					<template v-if="list.length > 0">

						<template v-for="(vo,keys) in list">
							<view class="flex-box flex-between mt30" @click.stop="shop_detail(vo.id)">
								<view class="fs30 cola lh42 fw500">{{vo.shop_name || ''}}
									<distance-format :distance="vo.distance"></distance-format>
								</view>
								<view class="fs30 cola">门店详情<image src="@/static/images/xilu_arrow_right.png"
										mode="widthFix" class="ico12 ml10">
									</image>
								</view>
							</view>
							<view class="">
								<view class="xilu_total_box mt30">
									<template v-if="vo.list.length > 0">
										<view class="xilu_total_box_item flex-box" v-for="(vv,kk) in vo.list"
											@tap="course_detail(vv.id,1)">
											<image :src="vv.course.xilufitness_urls.thumb_image" mode="aspectFill"
												class="xilu_total_box_cover">
											</image>
											<view class="flex-grow-1 pl20">
												<view class="fs30 fw500 colf lh42 m-ellipsis">{{vv.course.title || ''}}
												</view>
												<view class="mt10 lh32">
													<text
														class="fs24 colf">{{vv.start_at || ''}}～{{vv.end_at || ''}}</text>
													<text class="ml20 col2 fs24">¥{{vv.course_price || 0}}</text>
													<text class="fs20 col9"
														v-if="vv.market_price > 0">¥{{vv.market_price || 0}}</text>
												</view>
												<view class="mt10 fs24 col9 lh32"
													v-if="vv.course.lable_list.length > 0">
													{{vv.course.lable_list.join(' | ')}}
												</view>
											</view>
											<view class="xilu_btn1" v-if="vv.is_plan == 1">预约</view>
											<view class="xilu_btn1" v-else-if="vv.is_plan == 2">可排队</view>
											<view class="xilu_btn1" v-else-if="vv.is_plan == 3">已开始</view>
											<view class="xilu_btn1" v-else-if="vv.is_plan == 4">已售罄</view>
										</view>
									</template>
									<template v-else>
										<empty-data :tips="'暂无课程'" :lineHeight="200"></empty-data>
									</template>

								</view>
							</view>

						</template>

					</template>

					<template v-else>
						<empty-data :tips="'暂无课程'" :lineHeight="300"></empty-data>
					</template>

				</view>

			</template>

			<template v-if="course_type == 2">
				<!-- 私教 -->
				<template v-if="list.length > 0">

					<view class="pb30 plr25">
						<template v-if="list.length > 0">
							<template v-for="(vo,index) in list">
								<view class="flex-box ptb30" @click.stop="shop_detail(vo.id)">
									<view class="flex-grow-1 fs30 cola">{{vo.shop_name || ''}} <distance-format
											:distance="vo.distance"></distance-format></view>
									<view class="fs30 cola">门店详情</view>
									<image src="@/static/images/xilu_arrow_right.png" mode="widthFix"
										class="ico12 ml10">
									</image>
								</view>
								<view class="xilu_box1">
									<template v-if="vo.list.length > 0">

										<view class="xilu_box1_item flex-box" v-for="(vv,kk) in vo.list"
											@tap="course_detail(vv.id,2)">
											<image
												:src="vv.coach.xilufitness_urls.coach_avatar || '../../static/images/avatar.ong' "
												mode="aspectFill" class="xilu_box1_cover"></image>
											<view class="flex-grow-1 plr20">
												<view class="fs30 fw500 colf">{{vv.course.title || ''}}</view>
												<view class="mt10 lh32">
													<text class="fs24 col2">¥{{vv.course_price || 0}}</text><text
														class="fs20 tdl col9 ml5"
														v-if="vv.market_price > 0">¥{{vv.market_price || 0}}</text>
												</view>
												<view class="mt10 fs30 col9 lh42 m-ellipsis"
													v-if="vv.course.lable_list.length > 0">
													{{vv.course.lable_list.join('｜')}}
												</view>
											</view>
											<view class="xilu_btn" v-if="vv.is_plan == 1">预约</view>
											<view class="xilu_btn" v-else-if="vv.is_plan == 2">可排队</view>
											<view class="xilu_btn" v-else-if="vv.is_plan == 3">已开始</view>
											<view class="xilu_btn" v-else-if="vv.is_plan == 4">已售罄</view>
										</view>

									</template>

									<template v-else>
										<empty-data :tips="'暂无课程'" :lineHeight="100"></empty-data>
									</template>

								</view>
							</template>

						</template>

						<template v-else>
							<empty-data :tips="'暂无私教课程'" :lineHeight="100"></empty-data>
						</template>


					</view>

				</template>

				<template v-else>
					<empty-data :tips="'暂无课程'" :lineHeight="300"></empty-data>
				</template>

			</template>

			<template v-if="course_type == 3">
				<!-- 活动 -->
				<template v-if="list.length > 0">

					<view class="plr25">
						<template v-if="list.length > 0">
							<template v-for="(vo,keys) in list">
								<view class="flex-box ptb30" @click.stop="shop_detail(vo.id)">
									<view class="flex-grow-1 fs30 cola">{{vo.shop_name || ''}} <distance-format
											:distance="vo.distance"></distance-format></view>
									<view class="fs30 cola">门店详情</view>
									<image src="@/static/images/xilu_arrow_right.png" mode="widthFix"
										class="ico12 ml10">
									</image>
								</view>
								<template v-if="vo.list.length > 0">
									<view class="xilu_info_item flex-box" v-for="(vv,kk) in vo.list"
										@tap="camp_detail(vv.id)">
										<image :src="vv.camp.xilufitness_urls.thumb_image" mode="aspectFill"
											class="xilu_info_item_cover">
										</image>
										<view class="flex-grow-1 pl10">
											<view class="fs30 col2 fw500 lh42">{{vv.start_at || ''}}-{{vv.end_at || ''}}
											</view>
											<view class="flex flex-align-end">
												<view class="fs24 col9 lh34 mt15 m-ellipsis flex-grow-1">
													<text class="col2">¥{{vv.camp_price || 0}}</text>
													<text class="col9 tdl fs20 mlr5"
														v-if="vv.market_price > 0">¥{{vv.market_price || 0}}</text>
													| <text
														class="xilu_textsq colf mlr5">{{vv.class_count || 0}}课时</text>
													| <text
														class="xilu_textsq colf ml5">{{vv.class_duration || 0}}分钟/课时</text>
												</view>
												<view class="xilu_btn">报名</view>
											</view>
											<view class=" fs24 colf lh34" v-if="vv.plans.length > 0">
												<template v-for="(vvv,kkk) in vv.plans">
													{{vvv.week || ''}}{{vvv.day_start_at || ''}}～{{vvv.day_end_at || ''}}
												</template>

											</view>
										</view>
									</view>
								</template>
								<template v-else>
									<empty-data :tips="'暂无活动'" :lineHeight="100"></empty-data>
								</template>

							</template>
						</template>

						<template v-else>
							<empty-data :tips="'暂无活动'" :lineHeight="100"></empty-data>
						</template>

					</view>

				</template>

				<template v-else>
					<empty-data :tips="'暂无活动'" :lineHeight="300"></empty-data>
				</template>

			</template>

			<template v-if="course_type == 4">
				<!-- 收藏的教练 -->
				<view class="pb30 plr25">
					<template v-if="list.length > 0">
						<template v-for="(vo,index) in list">
							<view class="flex-box ptb30" @click.stop="shop_detail(vo.id)">
								<view class="flex-grow-1 fs30 cola">{{vo.shop_name || ''}} <distance-format
										:distance="vo.distance"></distance-format></view>
								<view class="fs30 cola">门店详情</view>
								<image src="@/static/images/xilu_arrow_right.png" mode="widthFix" class="ico12 ml10">
								</image>
							</view>
							<view class="xilu_box1">
								<template v-if="vo.coach_list.length > 0">

									<view class="xilu_box1_item flex-box" v-for="(vv,kk) in vo.coach_list"
										@tap="coach_detail(vv.id,vo.id)">
										<image :src="vv.xilufitness_urls.coach_avatar" mode="aspectFill"
											class="xilu_box1_cover">
										</image>
										<view class="flex-grow-1 plr20">
											<view class="fs30 fw500 colf">{{vv.coach_name || ''}}<text
													class="xilu_tips">{{vv.group_name || ''}}</text></view>
											<view class="mt25 fs30 col9 lh42 m-ellipsis"
												v-if="vv.lable_list && vv.lable_list.length > 0">
												{{vv.lable_list.join('｜')}}
											</view>
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
						<empty-data :tips="'暂无收藏教练'" :lineHeight="200"></empty-data>
					</template>
				</view>

			</template>



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
						<view class="xilu_popup_tab flex-box flex-center" :class="hindex==2?'active':''"
							@click="chooseHead(2)">
							<view>时间</view>
							<image src="@/static/images/xilu_arrow_yellow.png" mode="aspectFill" class="arrow"
								v-if="hindex==2"></image>
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
								v-for="(vo,index) in province_list" @click="chooseLeft(vo,'province')">{{vo.name}}
							</view>

						</scroll-view>
						<scroll-view class="xilu_popup_right flex-grow-1" scroll-y>
							<view class="xilu_right_item" :class="(city_id == 0 && area_id == 0) ?'active':''"
								@click="chooseRight(0,'city')">全部</view>

							<view class="xilu_right_item" :class="(city_id == vo.id || area_id == vo.id) ? 'active':''"
								v-for="(vo,index) in city_list" @click="chooseRight(vo,'city')">{{vo.name}}</view>
						</scroll-view>
					</view>
					<view class="xilu_popup_scroll" v-if="hindex==2">
						<view class="xilu_time_nav flex-box flex-center">
							<view class="xilu_time_link">开始时间</view>
							<view class="xilu_sq"></view>
							<view class="xilu_time_link">结束时间</view>
						</view>
						<scroll-view scroll-y="true" class="xilu_time_scroll">
							<view class="xilu_time_scroll_item" :class="tindex==index?'active':''"
								v-for="(vo,index) in timeList" @click="chooseTime(index,vo)">{{vo.title}}</view>
						</scroll-view>
					</view>
					<view class="p30 flex-box">
						<view @tap="resetData()" class="xilu_btn_reset">重置</view>
						<view @tap="clearData()" class="xilu_btn_sure">确认</view>
					</view>
				</view>
			</u-popup>

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
					title: '这里是课程列表',
					leftSlot: true,
					centerSlot: true,
					backgroundColor: [1, '#ffcf00'],
				},
				cindex: -1,
				show: false,
				hindex: 0,
				tindex: 0,
				timeList: [],
				classTimeList: [],
				choose_date: '',
				start_at: '',
				end_at: '',
				course_cate_list: [],
				course_cate_list_two: [],
				cate_pid: 0,
				cate_id: 0,
				province_list: [],
				city_list: [],
				province_id: 0,
				city_id: 0,
				area_id: 0,
				city_name: '',
				bannerList: [],
				course_type: 1,
				list: [],
				page: 1,
				total_count: 0,
				c_index: ''
			}
		},
		methods: {
			//选择课程类型
			chooseNav(course_type) {
				this.course_type = course_type
				this.clearData();
			},
			//选择城市
			changeCity(e) {
				let city_list = this.province_list;
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

			//选择搜索条件
			chooseClassNav(e) {
				if (e == 0) {
					this.getCourseCateList(0);
				} else if (e == 1) {
					this.getCityList(0);
				}
				this.cindex = e;
				this.hindex = e;
				this.show = true;
			},

			chooseHead(e) {
				if (e == 0 && this.course_cate_list.length == 0) {
					this.getCourseCateList(0);
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
					this.getCourseCateList(id)
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
			//选择时间
			chooseTime(index, times) {
				this.tindex = index,
					this.start_at = times.start_at;
				this.end_at = times.end_at;
			},
			//关闭弹窗
			closePop() {
				this.show = false;
			},
			//选择日期
			chooseDate(day_date) {
				this.choose_date = day_date;
				this.clearData();
			},
			//清楚数据
			clearData() {
				this.page = 1;
				this.list = [];
				this.total_count = 0;
				this.show = false;
				this.getLists();
			},
			//重置数据
			resetData() {
				this.province_id = 0;
				this.city_id = 0;
				this.area_id = 0;
				this.choose_date = '';
				this.cate_pid = 0;
				this.cate_id = 0;
				this.show = false;
				this.start_at = '';
				this.end_at = '';
				this.hindex = '';
				this.tindex = '';
				this.cindex = -1;
				this.clearData();
			},
			//获取数据
			getLists() {
				let _this = this;
				let url = '';
				if (this.course_type == 4) {
					url = '/addons/xilufitness/course/getCollectCoach';
				} else {
					url = '/addons/xilufitness/course/index';
				}
				this.$http({
					url: url,
					data: {
						lat: app.globalData.lat,
						lng: app.globalData.lng,
						province_id: _this.province_id,
						city_id: _this.city_id,
						area_id: _this.area_id,
						course_type: _this.course_type,
						choose_date: _this.choose_date,
						cate_pid: _this.cate_pid,
						cate_id: _this.cate_id,
						start_at: _this.start_at,
						end_at: _this.end_at,
						page: _this.page
					},
					method: 'post'
				}).then(res => {
					if (res.code) {
						_this.bannerList = res.data.bannerList;
						if (_this.page > 1) {
							_this.list.push(...res.data.list);
						} else {
							_this.list = res.data.list || [];
						}
						_this.classTimeList = res.data.classTimeList;
						_this.timeList = res.data.timeList;
						_this.total_count = res.data.total_count;
						_this.choose_date = _this.choose_date || res.data.day_date;
					}
				}).catch(error => {
					console.log('courseError', error);
				})
			},
			//获取课程分类
			getCourseCateList(pid) {
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
							_this.course_cate_list = res.data.list;
						} else {
							_this.course_cate_list_two = res.data.list;
						}
					}
				}).catch(error => {
					console.log('courseCateError', error);
				})
			},
			//获取城市数据
			getCityList(pid) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/home/getCityList',
					data: {
						pid: pid
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						if (pid == 0) {
							_this.province_list = res.data.list;
							if (!app.globalData.cityList) {
								app.globalData.cityList = res.data.list;
							}
						} else {
							_this.city_list = res.data.list;
						}
					}
				}).catch(error => {
					console.log('cityError', error);
				})
			},
			//门店详情
			shop_detail(id) {
				this.$api.navigate('../stores_info/stores_info?id=' + id)
			},
			//课程详情
			course_detail(id, is_type) {
				this.$api.navigate('../group_lessons_info/group_lessons_info?id=' + id + '&is_type=' + is_type)
			},
			//活动详情
			camp_detail(id) {
				this.$api.navigate('../bootcamp_info/bootcamp_info?id=' + id);
			},
			//教练详情
			coach_detail(id, shop_id) {
				this.$api.navigate('../trainer_info/trainer_info?id=' + id + '&shop_id=' + shop_id);
			},
			//banner点击跳转
			banner_redirect(url) {
				if (url) {
					this.$api.navigate(url);
				}
			}
		},
		onLoad(options) {
			let city_info = app.globalData.cityInfo;
			this.course_type = options.status || 1;
			this.city_id = city_info.id || 0;
			this.city_name = city_info.name || '';
			this.getCityList(0);
			this.getLists();
		},
		onShareAppMessage() {

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

		&_course_swiper {
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

		&_total_box {
			background: #404243;
			border-radius: 20rpx;
			padding: 0 20rpx;

			&_item {

				padding: 20rpx 0;
				border-bottom: 1px solid #666666;
			}

			&_item:last-child {
				border-bottom: none;
			}

			&_cover {
				width: 130rpx;
				height: 130rpx;
				border-radius: 20rpx;
				display: block;
			}
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

		&_btn1 {
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

		&_btn2 {
			width: 118rpx;
			height: 57rpx;
			line-height: 57rpx;
			font-size: 28rpx;
			text-align: center;
			font-weight: 400;
			color: #0E0E0F;
			background: #999999;
			border-radius: 10rpx;
		}

		&_btn3 {
			width: 118rpx;
			height: 57rpx;
			line-height: 53rpx;
			text-align: center;
			font-size: 28rpx;
			font-weight: 400;
			color: #FFFFFF;
			border-radius: 10rpx;
			border: 2rpx solid #999999;
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

		&_info_item {
			width: 700rpx;
			padding: 20rpx;
			background: #404243;
			border-radius: 20rpx;
			margin-bottom: 30rpx;

			&_cover {
				width: 150rpx;
				height: 150rpx;
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
	}
</style>
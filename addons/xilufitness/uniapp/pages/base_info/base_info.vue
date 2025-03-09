<template>
	<view class="xilu">
		<view class="page-foot bg-normal plr25">
			<view class="pb55" @click.stop="updateSetting()">
				<view class="btn1">确认修改</view>
			</view>
		</view>
		<view class="container plr30">
			<view class="xilu_box">
				<view class="xilu_head_nav" @tap="chooseAvatar()">
					<image :src="avatar || '../../static/images/avatar.png'" mode="aspectFill"
						class="xilu_head_nav_img">
					</image>
					<view class="xilu_head_nav_tips">更换
						头像</view>
				</view>
				<view class="mt55 flex-box">
					<view class="col2c fs32">昵称</view>
					<input type="text" value="Advens" class="flex-grow-1 tr fs32 colf pl10" placeholder="请输入"
						placeholder-class="cola" v-model="nickname" />
				</view>
				<view class="mt55 flex-box">
					<view class="col2c fs32">性别</view>
					<view class="flex-box flex-grow-1 flex-end">
						<view class="flex-box" @click="chooseSex(1)">
							<image :src="'../../static/images/xilu_male_'+(gender == 1 ? 's' : 'u')+'c.png'"
								mode="aspectFill" class="ico30 mr15"></image>
							<view class="colf fs30">男</view>
						</view>
						<view class="flex-box ml80" @click="chooseSex(2)">
							<image :src="'../../static/images/xilu_female_'+(gender == 2 ? 's' : 'u')+'c.png'"
								mode="aspectFill" class="ico30 mr15"></image>
							<view class="cola fs30">女</view>
						</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import uploads from "@/util/upload.js";
	var eventChannel = null;
	export default {
		data() {
			return {
				userInfo: null,
				nickname: '',
				avatar: '',
				gender: 0
			}
		},
		methods: {
			//选择性别
			chooseSex(sex) {
				this.gender = sex;
			},
			//上传图片
			chooseAvatar() {
				let _this = this;
				this.$api.chooseFile(1, function(res) {
					uploads.uploadFile(res[0]['tempFilePath'], function(uploadRes, uploadTask) {
						if (uploadRes.code == 1) {
							_this.avatar = uploadRes.data.fullurl;
						} else {
							_this.$api.toast(uploadRes.msg || '上传失败')
						}
					});
				})
			},
			//确认修改
			updateSetting() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/user/saveBaseInfo',
					data: {
						nickname: _this.nickname || '',
						gender: _this.gender || 0,
						avatar: _this.avatar || ''
					},
					method:'post'
				}).then(res => {
					if(res.code == 1){
						_this.$api.toast('保存成功');
						_this.$api.back(2000,1,function(){
							eventChannel.emit('reloadUserInfo',{});
						});
					} else {
						_this.$api.toast(res.msg);
					}
				}).catch(error => {
					console.log('updateUserInfoError',error);
				});
			}
		},
		onLoad() {
			let _this = this;
			eventChannel = this.getOpenerEventChannel();
			eventChannel.on('userBaseInfo', function(params) {
				_this.userInfo = params.userInfo;
				_this.avatar = params.userInfo.avatar;
				_this.nickname = params.userInfo.nickname;
				_this.gender = params.userInfo.gender;
			});
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_box {
			width: 690rpx;
			height: 80vh;
			background: #292B2C;
			border-radius: 20rpx;
			margin-top: 40rpx;
			padding-top: 60rpx;
			padding-left: 30rpx;
			padding-right: 30rpx;
		}

		&_head_nav {
			width: 150rpx;
			height: 150rpx;
			background: rgba(0, 0, 0, 0.3);
			display: block;
			margin-left: auto;
			margin-right: auto;
			position: relative;
			border-radius: 50%;

			&_img {
				width: 150rpx;
				height: 150rpx;
				border: 5rpx solid rgba(255, 255, 255, 0.3);
				position: relative;
				border-radius: 50%;
				z-index: 1;
			}

			&_tips {
				width: 150rpx;
				height: 150rpx;
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				padding: 33rpx 45rpx 0;
				border-radius: 50%;
				font-size: 30rpx;
				font-weight: 400;
				color: #FFFFFF;
				line-height: 42rpx;
				z-index: 2;
			}
		}
	}

	.pb55 {
		padding-bottom: 55rpx;
	}

	@supports (bottom: constant(safe-area-inset-bottom)) or (bottom: env(safe-area-inset-bottom)) {
		.container {
			padding-bottom: calc(68rpx/2);
			padding-bottom: calc(constant(safe-area-inset-bottom)/2);
			padding-bottom: calc(env(safe-area-inset-bottom)/2);
		}

		.page-foot {
			padding-bottom: 68rpx;
			padding-bottom: constant(safe-area-inset-bottom);
			padding-bottom: env(safe-area-inset-bottom);
		}

		.page-foot~.container {
			padding-bottom: calc(130rpx + 68rpx);
			padding-bottom: calc(constant(safe-area-inset-bottom));
			padding-bottom: calc(env(safe-area-inset-bottom));
		}
	}

	.mt55 {
		margin-top: 55rpx;
	}

	.col2c {
		color: #C2C2C2;
	}

	.ml80 {
		margin-left: 80rpx;
	}
</style>
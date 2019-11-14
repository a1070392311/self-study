# self-study
分享一个简单的分页方法，自己用的

# cert
windows 生成p12证书
双向认证https://blog.csdn.net/zkt286468541/article/details/80864184
nginx配置https.conf
    
	nginx配置强制跳转https 在80端口下配置 rewrite ^(.*)$  https://$host$1 permanent;
	
	添加srs https转发
	443端口配置
	location /live/ {
		proxy_pass http://127.0.0.1:8080/;
		#允许跨域
		add_header Access-Control-Allow-Origin *;  
	}
    location /api/ {
		proxy_pass http://127.0.0.1:9090/api/v1/streams/;
		add_header Access-Control-Allow-Origin *;
	}



普通的MP4切片hls命令
ffmpeg -y -i ./call.mp4 -vcodec copy -acodec copy -vbsf h264_mp4toannexb ./ts/call.ts
ffmpeg -i ./ts/call.ts -c copy -map 0 -f segment -segment_list ./m3u8/call.m3u8 -segment_time 5 ./m3u8/call-%03d.ts

ffmpeg -i input.mp4 -profile:v baseline -level 3.0 -s 640x360 -start_number 0 -hls_time 10 -hls_list_size 0 -f hls index.m3u8


mediainfo中格式概况是High@L4 和Main@L4的MP4格式视频可以正常播放
参考命令  ffmpeg -i ./1.mp4 -vf "scale=2*trunc(iw/2):-2,setsar=1" -profile:v high -pix_fmt yuv420p out2.mp4
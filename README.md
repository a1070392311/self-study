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
	}
    location /api/ {
		proxy_pass http://127.0.0.1:9090/api/v1/streams/;
	}

# elasticsearch es
## 安装
``shell
curl -O https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-8.6.1-darwin-x86_64.tar.gz
curl https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-8.6.1-darwin-x86_64.tar.gz.sha512 | shasum -a 512 -c - 
```
output ....:ok

```shell
tar -xvf elasticsearch-8.6.1-darwin-x86_64.tar.gz
cd elasticsearch-8.6.1/
```

## 测试安装
```shell
curl http://localhost:9200
```
有问题的话，执行 
lsof -i:9200 查看端口是否被占用，或者应用是否启动

如果返回 curl: (52) Empty reply from server
说明开启了 xpack.security.enabled:true //改成false
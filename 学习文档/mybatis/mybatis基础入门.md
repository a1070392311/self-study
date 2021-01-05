[toc "float:left"]
#安装

##1.安装
maven仓库查询地址 https://mvnrepository.com
使用maven构建项目，pom.xml加入代码
```xml
<dependency>
  <groupId>org.mybatis</groupId>
  <artifactId>mybatis</artifactId>
  <version>x.x.x</version>
</dependency>
<dependency>
    <groupId>mysql</groupId>
    <artifactId>mysql-connector-java</artifactId>
    <version>8.0.22</version>
</dependency>
```
##2.配置文件
创建一个基础的配置文件mybatis-config.xml于项目目录下
```xml
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE configuration
  PUBLIC "-//mybatis.org//DTD Config 3.0//EN"
  "http://mybatis.org/dtd/mybatis-3-config.dtd">
<configuration>
  <environments default="development">
    <environment id="development">
      <transactionManager type="JDBC"/>
      <dataSource type="POOLED">
        <property name="driver" value="${driver}"/>
        <property name="url" value="${url}"/>
        <property name="username" value="${username}"/>
        <property name="password" value="${password}"/>
      </dataSource>
    </environment>
  </environments>
  <mappers>
    <mapper resource="org/mybatis/example/BlogMapper.xml"/>
  </mappers>
</configuration>
```
##3.类文件中构建SqlSessionFactory对象
```java
//引入配置文件
String resource = "org/mybatis/example/mybatis-config.xml";
InputStream inputStream = Resources.getResourceAsStream(resource);
SqlSessionFactory sqlSessionFactory = new SqlSessionFactoryBuilder().build(inputStream);
```
##4.创建一个sql映射文件.xml
sql映射文件用于书写sql语句，在后面的接口化编程中用于和java接口文件对应
```xml
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE mapper
  PUBLIC "-//mybatis.org//DTD Mapper 3.0//EN"
  "http://mybatis.org/dtd/mybatis-3-mapper.dtd">
<mapper namespace="org.myproject.mapper.UserMapper">
  <!--
    namespace属性用于接口化编程时，对应相应的java接口类,所以不能随便定义
    id属性用于接口化编程时，对应相应的java接口类下定义的相关方法
    resultType 用于定义搜索结果的返回类型
  -->
  <select id="selectBlog" resultType="Blog">
    select * from Blog where id = #{id}
  </select>
</mapper>
```
##5.使用openssion进行查询
```java
//在前面获取到sqlSessionFactory的基础上
try (SqlSession session = sqlSessionFactory.openSession()) {
  UserMapper mapper = session.getMapper(UserMapper.class);
  //按照id查询
  Blog blog = mapper.selectBlog(101);
  //上面映射文件指定了namespace属性，所以也可以按照 命名空间+方法名 的方式调用
  Blog blog = (Blog) session.selectOne("org.myproject.mapper.UserMapper", 101);
}
```

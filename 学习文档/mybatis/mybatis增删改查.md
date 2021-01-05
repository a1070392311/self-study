#Mybatis CURD
[toc]
##基础增删改查
###映射文件内容
```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE mapper
  PUBLIC "-//mybatis.org//DTD Mapper 3.0//EN"
  "http://mybatis.org/dtd/mybatis-3-mapper.dtd">
  
  <mapper namespace="mapper.DeptMapper">
  	<cache eviction="FIFO"  flushInterval="60000"  size="512"  readOnly="true"/>
  	<insert id="insertDept" parameterType="mybatis.model.Dept"  useGeneratedKeys="true" keyProperty="id">
  		insert into dept(name,number) values (#{name},#{number})
  	</insert>
  	<select id="selectById" resultType="mybatis.model.Dept">
  		select * from dept where id=#{id}
  	</select>
  	<select id="selectByParam" resultType="mybatis.model.Dept">
  		select * from dept where name=#{name}
  	</select>
  	<select id="selectByMap" resultType="mybatis.model.Dept">
  		select * from dept where name=#{name}
  	</select>
  	<update id="updateDept" >
  		update dept set name=#{name},number=#{number} where id=#{id}
  	</update>
  	<delete id="deleteDeptByid">
  		delete from dept where id=#{id}
  	</delete>
  	<select id="selectAll" resultType="mybatis.model.Dept">
  		select * from dept
  	</select>
  	<select id="selectReturnMap" resultType="map">
  		select * from dept where id=#{id}
  	</select>
  	<select id="selectReturnlist" resultType="mybatis.model.Dept">
  		select * from dept
  	</select>
  </mapper>
  
```
###java接口文件内容
```java
package mapper;

import java.util.List;
import java.util.Map;

import org.apache.ibatis.annotations.MapKey;
import org.apache.ibatis.annotations.Param;

import mybatis.model.Dept;

public interface DeptMapper {
	public Dept selectById(int id);
	
	public Long insertDept(Dept dep);
	
	public Dept selectByParam(@Param("name")String name);
	
	public Dept selectByMap(Map<String, String> map);
	
	public void updateDept(Dept dep);
	
	public void deleteDeptByid(int id);
	
	public List<Dept> selectAll();
	
	public Map<String, String> selectReturnMap(int id);
    //MapKey 指定返回map集合的key是什么
	@MapKey("id")
	public Map<Integer , Dept> selectReturnlist();
}

```
###添加
```java
Dept dept = new Dept("测试", "3");
Long insertDept = mapper.insertDept(dept); //返回受影响行数
openSession.commit();
dept.getId(); //获取自增主键的值
openSession.close();
```
```xml
<!--
想要获取自增主键的值
配置useGeneratedKeys="true"  keyProperty="id"  这里是指定自增主键的对应javabean的属性名，改值会自动封装结果中
-->
<insert id="insertDept" parameterType="mybatis.model.Dept"  useGeneratedKeys="true" keyProperty="id">
    insert into dept(name,number) values (#{name},#{number})
</insert>
```
###删除
```java
mapper.deleteDeptByid(7);
openSession.commit(); 
openSession.close();
```
###修改
```java
Dept olddept = mapper.selectById(7);
olddept.setName("测试");
mapper.updateDept(olddept);
openSession.commit(); 
openSession.close();
```
###查询
```java
HashMap<String, String> hashMap = new HashMap<String, String>();
hashMap.put("name", "财务");
Dept dept = mapper.selectByMap(hashMap);
openSession.commit(); 
openSession.close();
```
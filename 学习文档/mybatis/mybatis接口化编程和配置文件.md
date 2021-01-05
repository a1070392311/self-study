[toc]
##mybatis接口化编程
&nbsp;&nbsp;&nbsp;&nbsp;mybatis中的sql映射文件和java接口进行动态绑定，根据sql映射文件中的namespace属性进行动态绑定
<font color=#f00 size=3 >所以namespace属性写对应的java接口文件的全类名即可</font>
<p>ep:映射文件dept.xml , 这里的映射文件要记得加到配置文件中才会自动匹配</p>
```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE mapper
  PUBLIC "-//mybatis.org//DTD Mapper 3.0//EN"
  "http://mybatis.org/dtd/mybatis-3-mapper.dtd">
  <!--
  		这里的namespace属性对应接口的类文件
  -->
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
<p>ep:对应的接口文件DeptMapper.java</p>
```java
public interface DeptMapper {
	public Dept selectById(int id);
	
	public Long insertDept(Dept dep);
	
	public Dept selectByParam(@Param("name")String name);
	
	public Dept selectByMap(Map<String, String> map);
	
	public void updateDept(Dept dep);
	
	public void deleteDeptByid(int id);
	
	public List<Dept> selectAll();
	
	public Map<String, String> selectReturnMap(int id);
	@MapKey("id")
	public Map<Integer , Dept> selectReturnlist();
}
```
<p>ep:对应mybatis-config.xml</p>
```xml
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE configuration
  PUBLIC "-//mybatis.org//DTD Config 3.0//EN"
  "http://mybatis.org/dtd/mybatis-3-config.dtd">
<configuration>
  <properties resource="conf/mybatis.properties">
  </properties>
  <environments default="development">
    <environment id="development">
      <transactionManager type="JDBC"/>
      <dataSource type="POOLED">
        <property name="driver" value="${mysql.driver}"/>
        <property name="url" value="${mysql.url}"/>
        <property name="username" value="${mysql.user}"/>
        <property name="password" value="${mysql.password}"/>
      </dataSource>
    </environment>
  </environments>
  <mappers>
    <mapper resource="mapper/companyarea.xml"/>
    <mapper resource="mapper/dept.xml"/>
  </mappers>
</configuration>
```
<p>ep:配置文件包含的mybatis.properties</p>
```
mysql.driver=com.mysql.cj.jdbc.Driver
mysql.url=jdbc:mysql://127.0.0.1:3306/test?serverTimezone=Asia/Shanghai
mysql.user=root
mysql.password=root
```



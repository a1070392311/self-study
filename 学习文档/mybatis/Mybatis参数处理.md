#Mybatis参数处理
[toc]
##原始类型或简单数据类型
原始类型或简单数据类型,数据不会被特殊处理,（比如 Integer 和 String）
```xml
<select id="selectUsers" parameterType="Integer" resultType="User">
  select id, username, password
  from users
  where id = #{id}
</select>
```
##model对象类型参数
对应属性值传参
```xml
<insert id="insertUser" parameterType="User">
  insert into users (id, username, password)
  values (#{id}, #{username}, #{password})
</insert>
```
参数也可以指定一个特殊的数据类型,例如
```xml
#{property,javaType=int,jdbcType=NUMERIC}
```

>参数可使用的属性
>>javaType: 参数对象的类型
>>>除非该对象是一个 HashMap。这个时候，你需要显式指定 javaType 来确保正确的类型处理器（TypeHandler）被使用

>>jdbcType: JDBC 类型
>>typeHandler: 特殊的类型处理器类（或别名）
>>numericScale:指定小数点后保留的位数。

###{}和${}的区别
>\#{}安全，转义，编译执行
>${}不安全，不转义，但是能动态添加，比如 ORDER BY 子句

###多参数传值
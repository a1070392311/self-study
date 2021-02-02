[toc]
#Spring AOP
##术语
* target：目标类，需要被代理的类
* joinpoint：连接点，指那些可能被拦截到的方法。例如：所有接口实现类的方法
* pointcut：切入点，已经被增强的连接点
* advice：通知/增强，例如after，before
* weaving：织入，把增强的advice应用到目标对象target来创建新的代理对象proxy的过程
* proxy：代理类
* aspect：切入点和通知的结合

##AOP手动实现的方式
###JDK动态代理
创建接口
```java
package test.aop;

public interface IUserService {
    public void AddUser();
    public void InsertUser();
    public void DeleteUser();
}

```
实现类
```java
package test.aop;

public class UserService implements IUserService{
    public void AddUser() {
        System.out.println("add a user");
    }

    public void InsertUser() {
        System.out.println("insert a user");
    }

    public void DeleteUser() {
        System.out.println("delete a user");
    }
}

```
切面类
```java
package test.aop;

public class MyAspect {
    public void before(){
        System.out.println("这里是方法执行之前的操作");
    }
    public void after(){
        System.out.println("这里是方法执行后面的操作");
    }
}

```
生成代理
```java
package test.aop;

import java.lang.reflect.InvocationHandler;
import java.lang.reflect.Method;
import java.lang.reflect.Proxy;

public class MyFactory {
    public static IUserService crateUserService(){
        //1 创建目标类
        final UserService userService = new UserService();
        //2 创建切面类
        final MyAspect myAspect = new MyAspect();
        //3 生成代理对象
        /*
        * 这一步将目标类和切面类结合，也是织入的过程
        * Proxy.newProxyInstance
        *   参数1 ：loader类加载器，动态代理类运行时创建，任何类都需要类加载器加载到内存
        *       一般情况  当前类.class.getClassLoader();      目标类对象.getClass.getClassLoader();
        *   参数2 ：interfaces 代理类需要实现的所有接口
        *       方式1 目标类对象.getClass.getInterfaces(); 这里只能获得自己的接口，不能获得父类的接口
        *       方式2 new Class[]{UserService.class} 创建一个class数组
        *   参数3 ： InvocationHandler 处理类，接口
        *       参数1 ： Object Proxy ：代理对象
        *       参数2 ： Method method ： 代理对象当前执行的方法
        *           获取方法名：method.getName();
        *           执行方法 ： method.invoke(对象,实际参数);
        *       参数3 ： Object[] args ： 方法实际参数
        * */

        //注意，这里用了多态的概念，接口类指向实现类，这里返回值是接口类型
        IUserService proxyUserService = (IUserService) Proxy.newProxyInstance(
                MyFactory.class.getClassLoader(),
                userService.getClass().getInterfaces(),
                new InvocationHandler() {
                    public Object invoke(Object proxy, Method method, Object[] args) throws Throwable {
                        myAspect.before();
                        //执行目标类的方法
                        Object invoke = method.invoke(userService, args);
                        myAspect.after();
                        return invoke;
                    }
                }
        );

        return proxyUserService;
    }
}

```
调用
```java
package test.aop;

public class Test {
    public static void main(String[] args) {
        IUserService userService = MyFactory.crateUserService();
        userService.AddUser();
        userService.DeleteUser();
        userService.InsertUser();

    }
}

```
结果
```
这里是方法执行之前的操作
add a user
这里是方法执行后面的操作
这里是方法执行之前的操作
delete a user
这里是方法执行后面的操作
这里是方法执行之前的操作
insert a user
这里是方法执行后面的操作
```

###Cglib动态代理
没有接口，只有实现类，在运行时创建目标类的子类，从而对目标类增强，就是说代理类是目标类的子类 
目标类
```java
package test.cglib;

public class TargetClass {
    public void needrun(){
        System.out.println("这里是目标类的方法");
    }
}

```
切面类
```java
package test.cglib;

public class Myaspect {
    public void before(){
        System.out.println("这里是方法执行之前的操作");
    }
    public void after(){
        System.out.println("这里是方法执行后面的操作");
    }
}

```
生成代理类
```java
package test.cglib;

import org.springframework.cglib.proxy.Enhancer;
import org.springframework.cglib.proxy.MethodInterceptor;
import org.springframework.cglib.proxy.MethodProxy;

import java.lang.reflect.Method;

public class CglibFactory {
    public static TargetClass createCglibProxy(){
        //1 生成实现类
        final TargetClass targetClass = new TargetClass();
        //2 生成切面类
        final Myaspect myaspect = new Myaspect();
        //3 生成代理
        Enhancer enhancer = new Enhancer();//核心类
        enhancer.setSuperclass(targetClass.getClass());//设置父类，也就是设置目标类
        //设置回调函数 ， MethodInterceptor等效于jdk代理中的InvocationHandler
        enhancer.setCallback(new MethodInterceptor() {
            public Object intercept(Object o, Method method, Object[] objects, MethodProxy methodProxy) throws Throwable {
                myaspect.before();
                Object invoke = method.invoke(targetClass, objects);
                //底下这句等价于上面那句
                //Object invoke1 = methodProxy.invoke(o, objects);
                myaspect.after();
                return invoke;
            }
        });
        TargetClass targetClass1 = (TargetClass) enhancer.create();
        return targetClass1;
    }
}

```
调用
```java
package test.cglib;

public class TestRun {
    public static void main(String[] args) {
        TargetClass cglibProxy = CglibFactory.createCglibProxy();
        cglibProxy.needrun();
    }
}

```
结果
```
这里是方法执行之前的操作
这里是目标类的方法
这里是方法执行后面的操作
```

##AOP通知联盟类型
* AOP联盟为通知Advice定义了org.aopalliance.Advice
* Spring按照通知Advice在目标类方法的连接点位置，可以分为5类
* 前置通知：org.springframework.aop.MethodBeforeAdvice	在目标方法执行前实施增强
* 后置通知：org.springframework.aop.AfterReturningAdvice	在目标方法执行后实施增强
* 环绕通知：org.springframework.intercept.MethodInterceptor	在目标方法执行前后实施增强
* 异常抛出通知：org.springframework.aop.ThrowsAdvice	在目标方法抛出异常后实施增强
* 引介通知：org.springframework.aop.IntroductionInterceptor	在目标类中添加一些新的方法和属性

##Spring半自动代理
接口
```java
package spring.half.auto;

public interface ITargetClass {
    public void needrun();
}

```
目标类
```java
package spring.half.auto;

public class TargetClass implements ITargetClass{
    public void needrun(){
        System.out.println("这里是目标类的方法");
    }
}

```
切面类
```java
package spring.half.auto;

import org.aopalliance.intercept.MethodInterceptor;
import org.aopalliance.intercept.MethodInvocation;

public class MyAscept implements MethodInterceptor {
    public Object invoke(MethodInvocation methodInvocation) throws Throwable {
        System.out.println("前1");
        Object proceed = methodInvocation.proceed();
        System.out.println("后1");
        return proceed;
    }
}

```
调用
```java
package spring.half.auto;

import org.springframework.context.ApplicationContext;
import org.springframework.context.support.ClassPathXmlApplicationContext;

public class RunBean {
    public static void main(String[] args) {
        ApplicationContext applicationContext = new ClassPathXmlApplicationContext("classpath:aop-bean.xml");
        //这里还是多态，要注意
        ITargetClass proxyFactoryBean = (ITargetClass) applicationContext.getBean("proxyFactoryBean");
        proxyFactoryBean.needrun();
    }
}

```
配置文件
```xml
<?xml version="1.0" encoding="UTF-8"?>
<beans xmlns="http://www.springframework.org/schema/beans"
       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xsi:schemaLocation="http://www.springframework.org/schema/beans http://www.springframework.org/schema/beans/spring-beans.xsd">
    <!--目标类-->
    <bean class="spring.half.auto.TargetClass" id="targetClass"></bean>
    <!--切面类-->
    <bean class="spring.half.auto.MyAscept" id="myAsceptii"></bean>
    <!--代理类
        使用工厂bean FactoryBean   ,底层调用getObject() , 返回特殊bean
        ProxyFactoryBean,用于创建代理工厂bean，生成特殊代理对象
            interfaces : 确定接口们
                通过<array>可以设置多个值
                只有一个值 value=""
            target: 确定目标类
            interceptorNames ：通知切面类的名称，类型String[] , 只有一个值 value=""
    -->
    <bean class="org.springframework.aop.framework.ProxyFactoryBean" id="proxyFactoryBean">
        <property name="interfaces" value="spring.half.auto.ITargetClass"></property>
        <property name="target" ref="targetClass"></property>
        <property name="interceptorNames" value="myAsceptii"></property>
    </bean>

</beans>
```
##Spring全自动代理
配置
```xml
<?xml version="1.0" encoding="UTF-8"?>
<beans xmlns="http://www.springframework.org/schema/beans"
       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xmlns:aop="http://www.springframework.org/schema/aop"
       xsi:schemaLocation="http://www.springframework.org/schema/beans
        http://www.springframework.org/schema/beans/spring-beans.xsd
        http://www.springframework.org/schema/aop
        http://www.springframework.org/schema/aop/spring-aop.xsd">
    <!--目标类-->
    <bean class="spring.half.auto.TargetClass" id="targetClass"></bean>
    <!--切面类-->
    <bean class="spring.half.auto.MyAscept" id="myAsceptii"></bean>

    <!--
    aop编程
        导入命名空间
        使用<aop:config>进行配置
            proxy-target-class="true" 声明使用cglib代理
          <aop:pointcut>切入点，从目标对象获得具体方法
            切入点表达式
            execution(返回值 包名.类名.方法名（方法参数，写两个点表示任意方法参数）)
          <aop:advisor>特殊的切面，只有一个通知和一个切入点
            advice-ref：通知引用
            pointcut-ref：切入点引用
    -->
    <aop:config proxy-target-class="true" >
        <aop:pointcut id="mypointcut" expression="execution(* spring.half.auto.*.*(..))"/>
        <aop:advisor advice-ref="myAsceptii" pointcut-ref="mypointcut" ></aop:advisor>
    </aop:config>
</beans>
```

##AspectJ AOP框架
###切入点表达式
1. execution()用于描述方法
	语法
    execution(修饰符 返回值 包.类.方法(参数)throws异常)
    	修饰符，一般省略
        	public 公共方法
            *	任意
        返回值
        	void 空值
            String 字符串
            * 任意
        包
        	com.my.pa 固定包
            com.my.*.service 子包任意
            com.my.pa.. 所有子包（含自己）
            com.my.pa.*.service.. 任意子包下的service所有子包（含自己）
        类
        	User 固定类
            *user 以user结尾
            User* 以User开头
            * 任意
        方法名，不能省略
        	addUser	固定
            add* 以add开头
            *do	以do结尾
            * 任意
       （参数）
       		()
            (int)
            (int,int)
            (..) 任意
2. AspectJ通知类型
	before 前置通知，用于各种效验
    afterReturning 后置通知，用于常规数据处理
    around 环绕通知，能做任何事情
    afterThrowing 抛出异常通知，用于包装异常信息
    after 最终通知，用于清理现场

##AspectJ AOP框架的XML使用方式
接口类
```java
package test.aspectXML;

public interface IUserService {
    public String AddUser();
    public String InsertUser();
    public String DeleteUser();
}

```
实体类
```java
package test.aspectXML;

public class UserService implements IUserService {
    public String AddUser() {
        System.out.println("add a user");
        return "AddUser";
    }

    public String InsertUser() {
        System.out.println("insert a user");
        return "InsertUser";
    }

    public String DeleteUser() {
        System.out.println("delete a user");
        return "DeleteUser";
    }
}

```
切面类
```java
package test.aspectXML;

import org.aspectj.lang.JoinPoint;
import org.aspectj.lang.ProceedingJoinPoint;

public class MyAspect {
    public void before(JoinPoint joinPoint){
        System.out.println("这里是方法执行之前的操作,当前执行方法名"+joinPoint.getSignature().getName());
    }
    public void after_returning(JoinPoint joinPoint , Object myreturn){
        System.out.println("这里后置通知方法,当前执行方法名"+joinPoint.getSignature().getName()+" 方法返回值"+myreturn.toString());
    }
    public void after(JoinPoint joinPoint){
        System.out.println("这里最终方法");
    }

    public Object around(ProceedingJoinPoint point) throws Throwable{
        System.out.println("前");
        Object proceed = point.proceed();
        System.out.println("后");
        return proceed;
    }

    public void thro(JoinPoint joinPoint , Throwable myth){
        System.out.println("这里是抛出异常操作"+myth.getMessage());
    }
}

```
调用类
```java
package test.aspectXML;

import org.springframework.context.ApplicationContext;
import org.springframework.context.support.ClassPathXmlApplicationContext;

public class RunBean {
    public static void main(String[] args) {
        ApplicationContext applicationContext = new ClassPathXmlApplicationContext("classpath:aop-aspectxml.xml");
        //这里还是多态，要注意
        IUserService userService = (IUserService) applicationContext.getBean("userService");
        userService.AddUser();
        userService.DeleteUser();
        userService.InsertUser();
    }
}

```
配置文件
```xml
<?xml version="1.0" encoding="UTF-8"?>
<beans xmlns="http://www.springframework.org/schema/beans"
       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xmlns:aop="http://www.springframework.org/schema/aop"
       xsi:schemaLocation="http://www.springframework.org/schema/beans
        http://www.springframework.org/schema/beans/spring-beans.xsd
        http://www.springframework.org/schema/aop
        http://www.springframework.org/schema/aop/spring-aop.xsd">
    <!--目标类-->
    <bean class="test.aspectXML.UserService" id="userService"></bean>
    <!--切面类-->
    <bean class="test.aspectXML.MyAspect" id="myAspect"></bean>
    <!--
    aop编程
    <aop:aspect>将切面类声明为切面，从而获得通知
        ref 切面类引用
    <aop:pointcut> 声明一个切入点，所有的通知都可以使用
        expression  切入点表达式
        id  名称，用于其他通知引用
    -->
    <aop:config>
        <aop:aspect ref="myAspect">
            <aop:pointcut id="mypointcut" expression="execution(* test.aspectXML.*.*(..))"/>
            <!--
            <aop:before>前置通知
                method 通知，即方法名
                pointcut 切入点表达式，只在当前通知使用
                pointcut-ref 切入点引用
            -->
            <!--<aop:before method="before" pointcut-ref="mypointcut"></aop:before>-->
            <!--
            后置通知，目标方法后执行，获取返回值
                method 通知，即方法名
                pointcut 切入点表达式，只在当前通知使用
                returning 设置通知方法第二个参数的参数名
            -->
            <!--<aop:after-returning method="after_returning" pointcut-ref="mypointcut" returning="myreturn" ></aop:after-returning>-->
            <!--
            环绕通知
                通知方法返回值类型：Object
                参数 类型 ProceedingJoinPoint
                方法要手动执行 Object proceed = point.proceed();
                方法要抛出异常
            -->
            <!--<aop:around method="around" pointcut-ref="mypointcut"></aop:around>-->

            <!--
            抛出异常通知
                throwing 通知方法的第二个变量的变量名
            -->
            <aop:after-throwing method="thro" pointcut-ref="mypointcut" throwing="myth"></aop:after-throwing>
            <!--
            后置通知 ， 最终执行的方法
            -->
            <!--<aop:after method="after" pointcut-ref="mypointcut"></aop:after>-->
        </aop:aspect>
    </aop:config>

</beans>
```

##AspectJ AOP框架的注解使用方式
接口类
```java
package test.aspectAnno;

public interface IUserService {
    public String AddUser();
    public String InsertUser();
    public String DeleteUser();
}

```
实现类
```java
package test.aspectAnno;

import org.springframework.stereotype.Service;

@Service("userService")
public class UserService implements IUserService {
    public String AddUser() {
        System.out.println("add a user");
        return "AddUser";
    }

    public String InsertUser() {
        System.out.println("insert a user");
        return "InsertUser";
    }

    public String DeleteUser() {
        System.out.println("delete a user");
        return "DeleteUser";
    }
}

```
切面类
```java
package test.aspectAnno;

import org.aspectj.lang.JoinPoint;
import org.aspectj.lang.ProceedingJoinPoint;
import org.aspectj.lang.annotation.*;
import org.springframework.stereotype.Component;

@Component
@Aspect
public class MyAspect {
    //这里使用方法名，相当于引用
    //@Before("Myspect()")
    public void before(JoinPoint joinPoint){
        System.out.println("这里是方法执行之前的操作,当前执行方法名"+joinPoint.getSignature().getName());
    }
    //声明切入点
    @Pointcut("execution(* test.aspectAnno.*.*(..))")
    private void Myspect(){

    }
    //@AfterReturning(value = "Myspect()", returning = "myreturn")
    public void after_returning(JoinPoint joinPoint , Object myreturn){
        System.out.println("这里后置通知方法,当前执行方法名"+joinPoint.getSignature().getName()+" 方法返回值"+myreturn.toString());
    }
    //@After("Myspect()")
    public void after(JoinPoint joinPoint){
        System.out.println("这里最终方法");
    }
    //@Around("Myspect()")
    public Object around(ProceedingJoinPoint point) throws Throwable{
        System.out.println("前");
        Object proceed = point.proceed();
        System.out.println("后");
        return proceed;
    }
    //@AfterThrowing(value = "execution(* test.aspectAnno.*.*(..))" , throwing = "myth")
    public void thro(JoinPoint joinPoint , Throwable myth){
        System.out.println("这里是抛出异常操作"+myth.getMessage());
    }
}

```
调用类
```java
package test.aspectAnno;

import org.springframework.context.ApplicationContext;
import org.springframework.context.support.ClassPathXmlApplicationContext;

public class RunBean {
    public static void main(String[] args) {
        ApplicationContext applicationContext = new ClassPathXmlApplicationContext("classpath:aop-aspectAnno.xml");
        //这里还是多态，要注意
        IUserService userService = (IUserService) applicationContext.getBean("userService");
        userService.AddUser();
        userService.DeleteUser();
        userService.InsertUser();
    }
}

```
配置文件
```xml
<?xml version="1.0" encoding="UTF-8"?>
<beans xmlns="http://www.springframework.org/schema/beans"
       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xmlns:aop="http://www.springframework.org/schema/aop"
       xmlns:context="http://www.springframework.org/schema/context"
       xsi:schemaLocation="http://www.springframework.org/schema/beans
        http://www.springframework.org/schema/beans/spring-beans.xsd
        http://www.springframework.org/schema/aop
        http://www.springframework.org/schema/aop/spring-aop.xsd
        http://www.springframework.org/schema/context
        http://www.springframework.org/schema/context/spring-context.xsd">
    <!--开启注解扫描-->
    <context:component-scan base-package="test.aspectAnno"></context:component-scan>
    <!--aspectj自动加载-->
    <aop:aspectj-autoproxy></aop:aspectj-autoproxy>
</beans>
```
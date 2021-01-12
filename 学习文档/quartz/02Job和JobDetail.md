#Job和JobDetail
[toc]
##实现一个job
只需要继承一个job接口并实现execute方法
```java
public class TestJob implements Job {

	@Override
	public void execute(JobExecutionContext context) throws JobExecutionException {
		// TODO Auto-generated method stub
		JobKey key = context.getJobDetail().getKey();
		Scheduler scheduler = context.getScheduler();
		try {
			SchedulerMetaData metaData = scheduler.getMetaData();
			SimpleDateFormat simpleDateFormat = new SimpleDateFormat("yyyy-mm-ss HH:mm:ss");
			Date date = new Date();
			String timeString = simpleDateFormat.format(date);
			System.out.println("This is my job , Time is "+timeString);
		} catch (SchedulerException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

}
```
##JobDataMap
如何给job实例增加属性或配置呢？
如何在job的多次执行中，跟踪job的状态呢？
答案就是:JobDataMap，JobDetail对象的一部分。
JobDataMap中可以包含不限量的（序列化的）数据对象，在job实例执行的时候，可以使用其中的数据；JobDataMap是Java Map接口的一个实现，额外增加了一些便于存取基本类型的数据的方法。

使用案例
```java
// define the job and tie it to our DumbJob class
  JobDetail job = newJob(DumbJob.class)
      .withIdentity("myJob", "group1") // name "myJob", group "group1"
      .usingJobData("jobSays", "Hello World!")
      .usingJobData("myFloatValue", 3.141f)
      .build();
```
job中两种获取jobdatamap的方法
```java
 public class DumbJob implements Job {

        public DumbJob() {
        }

        public void execute(JobExecutionContext context)
          throws JobExecutionException
        {
            JobKey key = context.getJobDetail().getKey();
			//直接获取map
            JobDataMap dataMap = context.getMergedJobDataMap();  // Note the difference from the previous example

            String jobSays = dataMap.getString("jobSays");
            float myFloatValue = dataMap.getFloat("myFloatValue");
            ArrayList state = (ArrayList)dataMap.get("myStateData");
            state.add(new Date());

            System.err.println("Instance " + key + " of DumbJob says: " + jobSays + ", and val is: " + myFloatValue);
        }
    }
```
```java
public class DumbJob implements Job {

	//设置属性，设置set方法，利用JobFactory实现数据的自动“注入“
    String jobSays;
    float myFloatValue;
    ArrayList state;

    public DumbJob() {
    }

    public void execute(JobExecutionContext context)
      throws JobExecutionException
    {
      JobKey key = context.getJobDetail().getKey();

      JobDataMap dataMap = context.getMergedJobDataMap();  // Note the difference from the previous example

      state.add(new Date());

      System.err.println("Instance " + key + " of DumbJob says: " + jobSays + ", and val is: " + myFloatValue);
    }

    public void setJobSays(String jobSays) {
      this.jobSays = jobSays;
    }

    public void setMyFloatValue(float myFloatValue) {
      myFloatValue = myFloatValue;
    }

    public void setState(ArrayList state) {
      state = state;
    }

  }
```

##Job状态与并发
>@DisallowConcurrentExecution
>将该注解加到job类上，告诉Quartz不要并发地执行同一个job定义（这里指特定的job类）的多个实例

>@PersistJobDataAfterExecution
>将该注解加在job类上，告诉Quartz在成功执行了job类的execute方法后（没有发生任何异常），更新JobDetail中JobDataMap的数据，使得该job（即JobDetail）在下一次执行的时候，JobDataMap中是更新后的数据，而不是更新前的旧数据。

>强烈建议你同时使用@DisallowConcurrentExecution注解，因为当同一个job（JobDetail）的两个实例被并发执行时，由于竞争，JobDataMap中存储的数据很可能是不确定的。


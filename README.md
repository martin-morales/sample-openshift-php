# OpenShift Advanced Demo

We are using Minishift on a Windows 10 Host using a VirtualBox driver. All examples assumes `oc`, and `odo` tools are installed and properly configured and that the Minishift VM has been properly started.

For help setting up Minishift please see OpenShift Introductory Demo cheatsheet or contact Martin Morales mmoral04@harris.com.



## Manual Example LAMP Stack

We use OpenShift to deploy a MySQL application and a PHP application built on the PHP Catalog Builder Component. We add some sample data into the database. Then we use OpenShift to add the MySQL secrets to the PHP application's environment variables. We verify that the our PHP app can communicate the database. Finally, we demonstrate how to make changes to the PHP app and deploy those changes.

#### Deploy a MySQL application

1. Get web console address: `oc status`
2. Log in as username: `developer` password: `developer`
3. From web console create a new project or click an existing project on the right side.
4. On the nav bar click Add to Project > Browse Catalog
5. Click on MySQL item
6. Click through popup. Configure username, password, database name if desired or generate them.

#### Add sample data

1. Get pods: `oc get pods`
2. Remote into pod: `oc rsh <podname>`
3. Open mysql session: `mysql -u $MYSQL_USER -p$MYSQL_PASSWORD -h $HOSTNAME $MYSQL_DATABASE`
4. Show mysql databases: `show databases;`
5. `use sampledb`
6. Create User table: `CREATE TABLE IF NOT EXISTS User (user_id INT AUTO_INCREMENT, name VARCHAR(255) NOT NULL, PRIMARY KEY (user_id));`
7. Add sample data: `INSERT INTO User (name) VALUES ('Jacob'), ('Erick'), ('John');`
8. Verify Data: `SELECT * FROM User;`
9. Exit mysql session: `exit`
10. Exit out of pod remote connection: `exit`

#### Deploy PHP application

1. From web console, on the nav bar click Add to Project > Browse Catalog
2. Click on PHP item
3. Name the app and for Git Repository enter `https://github.com/martin-morales/sample-openshift-php.git`

#### Add MySQL environment vars to PHP application build

1. On the left side, under Builds click Builds
2. Open the sample-openshift-php build
3. Click the Environment tab
4. Click Add Value from Config Map or Secret
5. For name enter `MYSQL_USER`, for resource select `mysql`, and for key select `database-user`
6. Repeat for MYSQL_PASSWORD, mysql, database-password
7. Repeat for MYSQL_DATABASE, mysql, database-name
8. At the top of the sample-openshift-php Build page click Start Build
   * This will build the Image, create a Deployment config, and deploy the Application.

#### Verify PHP application can communicate to the database

1. From the Overview tab, click the exposed route for the sample-openshift-php application.
   * If the route has not been created, click the expand caret and under Routes - External Traffic create a new route with the default settings.
2. You should see the web page with a list of users and the ability to add users.



## CI/CD Example LAMP Stack

1. Create project: `oc new-project <project-name>`

#### Migrate Repo to Gogs

1. Install Gogs: `oc process -f http://bit.ly/openshift-gogs-template --param=HOSTNAME=gogs-<project-name>.<openshift IP address>.nip.io --param=GOGS_VERSION=latest --param=SKIP_TLS_VERIFY=true | oc create -f -`
   * Won't work with the Harris proxy. I used my phone's hotspot.
   * You can use `minishift ip` to get the IP address
2. Create Gogs account with username: gogs password: gogs
3. After logging into Gogs, click the plus button by the username and click New Migration
4. For Clone Address input https://github.com/martin-morales/sample-openshift-php.git and Repository Name: `sample-openshift-php`
5. Edit sample-openshift-php/openshift/lamp-pipeline.yaml `templatePath` to point to the Gogs version of lamp-example-template.json

#### Setup Application

1. Create pipeline from template file: `oc new-app -f http://gogs-<project-name>.<openshift IP address>.nip.io/gogs/sample-openshift-php/raw/master/openshift/templates/lamp-pipeline-template.yaml`
2. Start the pipeline (will start once Jenkins Ephemeral is fully deployed and will start that deployment): `oc start-build lamp-pipeline`
   * This will take awhile to download the Jenkins agent centOS image for the first build

#### Add Webhook

1. Go to the OpenShift Web Console builds > pipelines
2. Open the lamp-example pipeline
3. Go to the configuration tab
4. Copy the GitHub webhook link
   * The Gogs webhook payload is identical as the GitHub one.
5. On Gogs, open the repository settings
6. On the Webhooks tab click Add Webhook of type Gogs
7. Paste the webhook URL to the payload URL. Ensure the Content Type is `application/json`
8. Setup when the webhook should be trigged and add the webhook.

#### Verify CI/CD

1. Make an edit to the Gogs repo of the project
2. Verify that a new build has started on OpenShift Web Console builds > pipelines or jenkins
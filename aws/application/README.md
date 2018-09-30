This assumes that you are running a Unix-y shell, and you have:

* the AWS CLI tools installed
* suitable AWS configuration to use the tools to talk to the Acas AWS estate
* environment variables set for the account you're working with
  * ACAS_ACCOUNT - the AWS account number
  * ACAS_ENV – the Acas environment (one of `pre-prod` or `prod`)

You can get the AWS account number running something like this:

```sh
aws sts get-caller-identity --output text --query 'Account' --profile acas-pre-prod
```

First, you will need to package up your changes so that they are ready
for deployment.

```sh
aws cloudformation package --template-file "$(pwd)/application/monitoring_stack.template" \
    --s3-bucket "acas-cfn-${ACAS_ACCOUNT}-eu-west-1" --s3-prefix "notify/$ACAS_ENV" \
    --output-template-file "$(pwd)/application/notify-monitoring-${ACAS_ENV}.packaged" \
    --profile "acas-${ACAS_ENV}"
```

Next, deploy the stack. You might need to use `--parameter-overrides` to
customise behaviour for different environments.

```sh
aws cloudformation deploy --stack-name "ACAS-notify-monitoring-${ACAS_ENV}" \
    --template-file "$(pwd)/application/notify-monitoring-${ACAS_ENV}.packaged" \
    --s3-bucket "acas-cfn-${ACAS_ACCOUNT}-eu-west-1" --s3-prefix "notify/$ACAS_ENV" \
    --no-execute-changeset \
    --capabilities CAPABILITY_NAMED_IAM \
    --profile "acas-${ACAS_ENV}"
```

Take note of the change set name that it returned as use that:

```sh
aws cloudformation describe-change-set \
    --change-set-name INSERT_CHANGE_SET_NAME
    --profile "acas-${ACAS_ENV}"
```

If you wish to proceed, then execute the change set.

```sh
aws cloudformation execute-change-set \
    --change-set-name INSERT_CHANGE_SET_NAME
    --profile "acas-${ACAS_ENV}"
```

Alternatively, delete the change set and try again.

```sh
aws cloudformation delete-change-set \
    --change-set-name INSERT_CHANGE_SET_NAME
    --profile "acas-${ACAS_ENV}"
```

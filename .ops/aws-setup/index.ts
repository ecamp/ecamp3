import { AccessKey, Policy, User, UserPolicyAttachment } from '@pulumi/aws/iam'
import {
  Bucket,
  BucketAcl,
  BucketLifecycleConfiguration,
  BucketObjectLockConfiguration,
  BucketVersioning,
} from '@pulumi/aws/s3'
import { Config, interpolate } from '@pulumi/pulumi'

const config = new Config()
const environment = config.require('env') || 'dev'

const retentionPolicies = {
  transitions: [
    {
      days: 30,
      storageClass: 'GLACIER',
    },
  ],
  expiration: {
    days: 365,
  },
}

let objectLockRetentionDays = 365
if (environment === 'dev') {
  retentionPolicies.transitions[0].days = 1
  retentionPolicies.expiration.days = 7
  objectLockRetentionDays = 8
}

const backupBucket = new Bucket(`ecamp3-${environment}-bucket`, {})

new BucketAcl(`ecamp3-${environment}-bucket-acl`, {
  bucket: backupBucket.id,
  acl: 'private',
})

new BucketVersioning(`ecamp3-${environment}-bucket-versioning`, {
  bucket: backupBucket.id,
  versioningConfiguration: {
    status: 'Enabled',
  },
})

new BucketLifecycleConfiguration(`ecamp3-${environment}-bucket-lifecycle`, {
  bucket: backupBucket.id,
  rules: [
    {
      id: 'retention',
      status: 'Enabled',
      filter: {},
      abortIncompleteMultipartUpload: {
        daysAfterInitiation: 1,
      },
      ...retentionPolicies,
    },
  ],
})

new BucketObjectLockConfiguration(`ecamp3-${environment}-bucket-object-lock`, {
  bucket: backupBucket.id,
  rule: {
    defaultRetention: {
      mode: 'GOVERNANCE',
      days: objectLockRetentionDays,
    },
  },
})

const putObjectPolicy = new Policy('put-object', {
  policy: {
    Version: '2012-10-17',
    Statement: [
      {
        Effect: 'Allow',
        Action: 's3:PutObject',
        Resource: [backupBucket.arn, interpolate`${backupBucket.arn}/*`],
      },
    ],
  },
})

const putOnlyUser = new User(`ecamp3-${environment}-put-only-user`, {
  name: `ecamp3-${environment}-put-only-user`,
  permissionsBoundary: putObjectPolicy.arn,
})

const putOnlyUserAccessKey = new AccessKey(
  `ecamp3-${environment}-put-only-user-access-key`,
  {
    user: putOnlyUser.name,
  }
)

const downloadObjectPolicy = new Policy('download-object', {
  policy: {
    Version: '2012-10-17',
    Statement: [
      {
        Effect: 'Allow',
        Action: ['s3:GetObjectVersion', 's3:ListBucket', 's3:ListBucketVersions'],
        Resource: [backupBucket.arn, interpolate`${backupBucket.arn}/*`],
      },
    ],
  },
})

const downloadOnlyUser = new User(`ecamp3-${environment}-download-only-user`, {
  name: `ecamp3-${environment}-download-only-user`,
  permissionsBoundary: downloadObjectPolicy.arn,
})

const downloadOnlyAccessKey = new AccessKey(
  `ecamp3-${environment}-download-only-user-access-key`,
  {
    user: downloadOnlyUser.name,
  }
)

new UserPolicyAttachment(`ecamp3-${environment}-put-only-policy-attachment`, {
  user: putOnlyUser.name,
  policyArn: putObjectPolicy.arn,
})

new UserPolicyAttachment(`ecamp3-${environment}-download-only-policy-attachment`, {
  user: downloadOnlyUser.name,
  policyArn: downloadObjectPolicy.arn,
})

// noinspection JSUnusedGlobalSymbols
export const bucketEndpoint = backupBucket.bucketDomainName
// noinspection JSUnusedGlobalSymbols
export const bucketName = backupBucket.bucket

// noinspection JSUnusedGlobalSymbols
export const putOnlyUserAccessKeyId = putOnlyUserAccessKey.id
// noinspection JSUnusedGlobalSymbols
export const putOnlyUserSecretAccessKey = putOnlyUserAccessKey.secret

// noinspection JSUnusedGlobalSymbols
export const downloadOnlyUserAccessKeyId = downloadOnlyAccessKey.id
// noinspection JSUnusedGlobalSymbols
export const downloadOnlyUserSecretAccessKey = downloadOnlyAccessKey.secret

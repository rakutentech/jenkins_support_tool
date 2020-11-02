# Jenkins Support Tool

Provide support tool to reduce jenkins operation, easy to use , etc.

## Tool list

- Jenkins Tree

## Benefit

- set job status "Enable" or "Disable" in bulk
When Job is set status "Enable" or "Disable", you need to set job status one by one.
But this tool support you to set many job status in bulk.
<IMG SRC="https://qiita-image-store.s3.ap-northeast-1.amazonaws.com/0/267085/7a7a19b0-d4a5-3779-1a07-9676c438b0ca.png">




## How to build

### Jenkins Tree

#### Environment

- PHP Version 7.3.7
- Smarty Version 3.1
- Jenkins Version 2.250

#### Jenkins Side

- set "Global Security" permission "Allow anonymous read access"  is "available"

<IMG SRC="https://qiita-user-contents.imgix.net/https%3A%2F%2Fqiita-image-store.s3.ap-northeast-1.amazonaws.com%2F0%2F267085%2Fe065c919-2b44-fc1b-30c1-3d292de38ac6.png?ixlib=rb-1.2.2&auto=format&gif-q=60&q=75&s=3ae0edd0b0c765b0193ccda585e0abb3" WIDTH=80%>

- create API token

<IMG SRC="https://qiita-user-contents.imgix.net/https%3A%2F%2Fqiita-image-store.s3.ap-northeast-1.amazonaws.com%2F0%2F267085%2F1bf5352f-46c1-6a67-ff55-c3ea1a933744.png?ixlib=rb-1.2.2&auto=format&gif-q=60&q=75&s=5f126a5b68b95baf3f099c47ae4da24c" WIDTH=80%>

#### conf_oss.ini setting

modify conf_oss.ini file to adjust your jenkins envrionment & token information

Detail information is here
https://qiita.com/emurin/items/b81241d159e69ab5c74e






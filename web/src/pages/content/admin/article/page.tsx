import { useTranslate, useList, useResource } from '@refinedev/core'
import {
  FormPage,
  formatUploadSingle,
  getUploadSingle,
  useUpload,
  Editor,
} from '@duxweb/dux-refine'
import { Form, Input, Upload, Radio, Cascader } from 'tdesign-react/esm'

const Page = () => {
  const uploadParams = useUpload()
  const translate = useTranslate()
  const { id } = useResource()
  const { data, isLoading } = useList({
    resource: 'content.category',
  })
  const list = data?.data || []

  return (
    <FormPage
      formProps={{
        labelAlign: 'top',
      }}
      back
      id={id}
      initFormat={(data) => {
        data.image = formatUploadSingle(data.image)
        return data
      }}
      saveFormat={(data) => {
        data.image = getUploadSingle(data.image)
        return data
      }}
      settingRender={
        <>
          <Form.FormItem label={translate('content.article.fields.category')} name='class_id'>
            <Cascader
              loading={isLoading}
              options={list}
              keys={{
                label: 'name',
                value: 'id',
              }}
              clearable
              checkStrictly
            />
          </Form.FormItem>
          <Form.FormItem label={translate('content.article.fields.subtitle')} name='subtitle'>
            <Input />
          </Form.FormItem>
          <Form.FormItem label={translate('content.article.fields.image')} name='image'>
            <Upload {...uploadParams} theme='image' accept='image/*' />
          </Form.FormItem>
          <Form.FormItem label={translate('content.article.fields.author')} name='author'>
            <Input />
          </Form.FormItem>
          <Form.FormItem
            label={translate('content.article.fields.status')}
            name='status'
            initialData={true}
          >
            <Radio.Group>
              <Radio value={true}>{translate('content.article.tab.published')}</Radio>
              <Radio value={false}>{translate('content.article.tab.unpublished')}</Radio>
            </Radio.Group>
          </Form.FormItem>
        </>
      }
    >
      <Form.FormItem name='title'>
        <Input size='large' placeholder={translate('content.article.validate.title')} />
      </Form.FormItem>

      <Form.FormItem name='content'>
        <Editor />
      </Form.FormItem>
    </FormPage>
  )
}

export default Page

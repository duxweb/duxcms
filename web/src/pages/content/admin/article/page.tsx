import { useTranslate, useList, useResource } from '@refinedev/core'
import {
  FormPage,
  Editor,
  useClient,
  UploadImage,
  useSelect,
  FormPageItem,
} from '@duxweb/dux-refine'
import { Form, Input, Radio, Cascader, AutoComplete, Textarea, Checkbox } from 'tdesign-react/esm'
import { useEffect, useState } from 'react'
import { MagicFormRender } from '@duxweb/dux-extend'

const Page = () => {
  const translate = useTranslate()
  const { id } = useResource()
  const { data, isLoading } = useList({
    resource: 'content.category',
  })
  const list = data?.data || []
  const [form] = Form.useForm()
  const classId = Form.useWatch('class_id', form)
  const { request } = useClient()
  const [magic, setMagic] = useState<Record<string, any>>()

  useEffect(() => {
    if (!classId) {
      setMagic(undefined)
      return
    }
    request(`content/category/${classId}/magic`, 'get').then((res) => {
      setMagic(res?.data || {})
    })
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [classId])

  const { options: sourceData } = useSelect({
    resource: 'content.source',
    optionLabel: 'name',
    optionValue: 'id',
  })

  const { options: recommendData } = useSelect({
    resource: 'content.recommend',
    optionLabel: 'name',
    optionValue: 'id',
  })

  return (
    <FormPage
      formProps={{
        labelAlign: 'top',
      }}
      back
      form={form}
      id={id}
      settingRender={
        <>
          <Form.FormItem label={translate('content.article.fields.subtitle')} name='subtitle'>
            <Input />
          </Form.FormItem>
          <Form.FormItem label={translate('content.article.fields.image')} name='images'>
            <UploadImage multiple accept='image/*' />
          </Form.FormItem>

          <Form.FormItem
            label={translate('content.article.fields.imagesAuto')}
            name='images_auto'
            initialData={true}
          >
            <Radio.Group>
              <Radio value={true}>{translate('content.article.fields.auto')}</Radio>
              <Radio value={false}>{translate('content.article.fields.manual')}</Radio>
            </Radio.Group>
          </Form.FormItem>

          <Form.FormItem label={translate('content.article.fields.recommend')} name='recommend'>
            <Checkbox.Group options={recommendData} />
          </Form.FormItem>

          <Form.FormItem label={translate('content.article.fields.source')} name='source'>
            <AutoComplete options={sourceData} highlightKeyword filterable={false} clearable />
          </Form.FormItem>

          {magic?.fields && <MagicFormRender fields={magic?.fields} prefix='extend' />}

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
      <FormPageItem name='class_id'>
        <Cascader
          loading={isLoading}
          options={list}
          keys={{
            label: 'name',
            value: 'id',
          }}
          clearable
          checkStrictly
          placeholder={translate('content.article.validate.class')}
        />
      </FormPageItem>

      <FormPageItem name='title'>
        <Input placeholder={translate('content.article.validate.title')} />
      </FormPageItem>

      <FormPageItem name='content'>
        <Editor />
      </FormPageItem>

      <FormPageItem name='keyword'>
        <Input placeholder={translate('content.article.validate.keywords')} />
      </FormPageItem>

      <FormPageItem name='descriptions'>
        <Textarea placeholder={translate('content.article.validate.descriptions')} />
      </FormPageItem>
    </FormPage>
  )
}

export default Page

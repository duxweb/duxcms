import { useTranslate, useList } from '@refinedev/core'
import { FormModal, UploadImageManage } from '@duxweb/dux-refine'
import { Form, Input, Cascader } from 'tdesign-react/esm'

const Page = (props: Record<string, any>) => {
  const translate = useTranslate()
  const menuId = props.menu_id
  const { data, isLoading } = useList({
    resource: 'content.menuData',
    meta: {
      params: {
        menu_id: menuId,
      },
    },
  })
  const list = data?.data || []

  return (
    <FormModal
      id={props?.id}
      saveFormat={(data) => {
        return {
          ...data,
          menu_id: menuId,
        }
      }}
      queryParams={{
        menu_id: menuId,
      }}
    >
      <Form.FormItem label={translate('content.menu.fields.parent')} name='parent_id'>
        <Cascader
          checkStrictly
          loading={isLoading}
          options={list}
          keys={{
            label: 'title',
            value: 'id',
          }}
          clearable
        />
      </Form.FormItem>
      <Form.FormItem label={translate('content.menu.fields.title')} name='title'>
        <Input />
      </Form.FormItem>
      <Form.FormItem label={translate('content.menu.fields.subtitle')} name='subtitle'>
        <Input />
      </Form.FormItem>
      <Form.FormItem label={translate('content.menu.fields.url')} name='url'>
        <Input />
      </Form.FormItem>
      <Form.FormItem label={translate('content.menu.fields.image')} name='image'>
        <UploadImageManage accept='image/*' />
      </Form.FormItem>
    </FormModal>
  )
}

export default Page

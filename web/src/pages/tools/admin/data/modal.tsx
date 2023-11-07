import { useCustom, useTranslate, useList } from '@refinedev/core'
import { FormModal } from '@duxweb/dux-refine'
import { MagicFormRender } from '@duxweb/dux-extend'
import { Form, Cascader } from 'tdesign-react/esm'

const Page = (props: Record<string, any>) => {
  const magic = props.magic

  const translate = useTranslate()

  const { data } = useCustom<Record<string, any>>({
    url: 'tools/magic/config',
    method: 'get',
    meta: {
      params: {
        magic: magic,
      },
    },
  })

  const { data: treeData, isLoading } = useList({
    resource: 'tools.data',
    meta: {
      params: {
        magic: magic,
      },
    },
    pagination: {
      mode: 'off',
    },
  })

  return (
    <FormModal
      queryParams={{
        magic: magic,
      }}
      id={props?.id}
    >
      {data?.data?.type == 'tree' && (
        <Form.FormItem label={translate('tools.data.fields.parent')} name='parent_id'>
          <Cascader
            checkStrictly
            loading={isLoading}
            options={treeData?.data}
            keys={{
              label: data?.data?.tree_label || 'name',
              value: 'id',
            }}
            clearable
          />
        </Form.FormItem>
      )}
      {data?.data?.fields && <MagicFormRender fields={data?.data?.fields} />}
    </FormModal>
  )
}

export default Page

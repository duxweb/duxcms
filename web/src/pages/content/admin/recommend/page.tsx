import { useTranslate, useResource } from '@refinedev/core'
import {
  FormPage,
  FormPageItem,
  ListSelect,
  MediaText,
  FilterItem,
  CascaderAsync,
} from '@duxweb/dux-refine'
import { Form, Input, Tag } from 'tdesign-react/esm'

const Page = () => {
  const translate = useTranslate()
  const { id } = useResource()
  const [form] = Form.useForm()

  return (
    <FormPage back form={form} id={id}>
      <FormPageItem
        label={translate('content.recommend.fields.name')}
        name='name'
        help={translate('content.recommend.help.name')}
      >
        <Input />
      </FormPageItem>
      <FormPageItem
        label={translate('content.recommend.fields.articles')}
        name='articles'
        help={translate('content.recommend.help.articles')}
      >
        <ListSelect
          sort
          resource={'content.article'}
          options={[
            {
              field: 'id',
              title: 'ID',
              width: 100,
              content: ({ row }) => {
                return row.id
              },
            },
            {
              title: translate('content.article.fields.title'),
              field: 'title',
              content: ({ row }) => {
                return (
                  <MediaText size='small'>
                    <MediaText.Image src={row?.images?.[0]} />
                    <MediaText.Title>{row?.title}</MediaText.Title>
                    <MediaText.Desc>{row?.subtitle}</MediaText.Desc>
                  </MediaText>
                )
              },
            },
            {
              title: translate('content.article.fields.status'),
              field: 'status',
              width: 100,
              content: ({ row }) => {
                return (
                  <>
                    {row.status ? (
                      <Tag theme='warning' variant='outline'>
                        {translate('content.article.tab.published')}
                      </Tag>
                    ) : (
                      <Tag theme='success' variant='outline'>
                        {translate('content.article.tab.unpublished')}
                      </Tag>
                    )}
                  </>
                )
              },
            },
          ]}
          filterRender={
            <>
              <FilterItem name='class_id'>
                <CascaderAsync
                  placeholder={translate('content.article.validate.class')}
                  url={'content/category'}
                  keys={{
                    label: 'name',
                    value: 'id',
                  }}
                  format={(v) => parseInt(v)}
                  filterable
                  checkStrictly
                  clearable
                />
              </FilterItem>
            </>
          }
          tableColumns={[
            {
              colKey: 'id',
              title: 'ID',
              width: 100,
            },
            {
              colKey: 'title',
              title: translate('content.article.fields.title'),
              cell: ({ row }) => {
                return (
                  <MediaText size='small'>
                    <MediaText.Image src={row?.images?.[0]} />
                    <MediaText.Title>{row?.title}</MediaText.Title>
                    <MediaText.Desc>{row.class_name?.join(' > ')}</MediaText.Desc>
                  </MediaText>
                )
              },
            },
            {
              colKey: 'status',
              title: translate('content.article.fields.status'),
              width: 100,
              cell: ({ row }) => {
                return (
                  <>
                    {row.status ? (
                      <Tag theme='warning' variant='outline'>
                        {translate('content.article.tab.published')}
                      </Tag>
                    ) : (
                      <Tag theme='success' variant='outline'>
                        {translate('content.article.tab.unpublished')}
                      </Tag>
                    )}
                  </>
                )
              },
            },
          ]}
        />
      </FormPageItem>
    </FormPage>
  )
}

export default Page

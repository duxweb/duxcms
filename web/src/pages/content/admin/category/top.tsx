import { useTranslate } from '@refinedev/core'
import { FormModal, ListSelect, MediaText } from '@duxweb/dux-refine'
import { Form, Tag } from 'tdesign-react/esm'

const Page = (props: Record<string, any>) => {
  const translate = useTranslate()

  {console.log(props?.id)}
  return (
    <FormModal id={props?.id} resource='content/category/top'>
      <Form.FormItem name='tops'>
        <ListSelect
          sort
          resource={'content/article?class_id=' + props?.id}

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
                    <MediaText.Image src={row?.images[0]} />
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
                    <MediaText.Image src={row?.images[0]} />
                    <MediaText.Title>{row?.title}</MediaText.Title>
                    <MediaText.Desc>{row?.subtitle}</MediaText.Desc>
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
      </Form.FormItem>
    </FormModal>
  )
}

export default Page

import React from 'react'
import { useTranslate, useDelete, useNavigation } from '@refinedev/core'
import { PrimaryTableCol, Button, Input, Tag, Link, Popconfirm } from 'tdesign-react/esm'
import { PageTable, FilterItem, MediaText } from '@duxweb/dux-refine'

const List = () => {
  const translate = useTranslate()
  const { mutate } = useDelete()
  const { create, edit } = useNavigation()

  const columns = React.useMemo<PrimaryTableCol[]>(
    () => [
      { colKey: 'row-select', type: 'multiple' },
      {
        colKey: 'id',
        sorter: true,
        sortType: 'all',
        title: 'ID',
        width: 100,
      },
      {
        colKey: 'title',
        title: translate('content.article.fields.title'),
        ellipsis: true,
        cell: ({ row }) => {
          return (
            <MediaText size='small'>
              <MediaText.Image src={row.image}></MediaText.Image>
              <MediaText.Title>{row.title}</MediaText.Title>
              <MediaText.Desc>{row.subtitle}</MediaText.Desc>
            </MediaText>
          )
        },
      },
      {
        colKey: 'author',
        title: translate('content.article.fields.author'),
        minWidth: 200,
      },
      {
        colKey: 'status',
        title: translate('content.article.fields.status'),
        width: 150,
        filter: {
          type: 'single',
          list: [
            { label: translate('content.article.tab.published'), value: '1' },
            { label: translate('content.article.tab.unpublished'), value: '2' },
          ],
        },
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
      {
        colKey: 'created_at',
        title: translate('content.article.fields.createdAt'),
        sorter: true,
        sortType: 'all',
        width: 200,
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 120,
        cell: ({ row }) => {
          return (
            <div className='flex justify-center gap-4'>
              <Link theme='primary' onClick={() => edit('content.article', row.id)}>
                {translate('buttons.edit')}
              </Link>
              <Popconfirm
                content={translate('buttons.confirm')}
                destroyOnClose
                placement='top'
                showArrow
                theme='default'
                onConfirm={() => {
                  mutate({
                    resource: 'article',
                    id: row.id,
                  })
                }}
              >
                <Link theme='danger'>{translate('buttons.delete')}</Link>
              </Popconfirm>
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate],
  )

  return (
    <PageTable
      columns={columns}
      tabs={[
        {
          label: translate('content.article.tab.all'),
          value: '0',
        },
        {
          label: translate('content.article.tab.published'),
          value: '1',
        },
        {
          label: translate('content.article.tab.unpublished'),
          value: '2',
        },
      ]}
      actionRender={() => (
        <Button
          icon={<div className='i-tabler:plus t-icon'></div>}
          onClick={() => {
            create('content.article')
          }}
        >
          {translate('buttons.create')}
        </Button>
      )}
      filterRender={() => {
        return (
          <>
            <FilterItem name='keyword'>
              <Input />
            </FilterItem>
          </>
        )
      }}
    />
  )
}

export default List
